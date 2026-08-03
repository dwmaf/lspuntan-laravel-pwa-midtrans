<?php

namespace App\Traits;

use App\Models\User;
use App\Models\NotificationLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Exception\Messaging\NotFound;

trait SendsPushNotifications
{
    /**
     * @param Messaging $messaging
     * @param User $recipient
     * @param string $title
     * @param string $body
     * @param string $url
     * @param string $type
     */
    protected function sendPushNotification(
        Messaging $messaging,
        ?User $recipient,
        string $title,
        string $body,
        string $url,
        string $type
    ): void {
        if (!$recipient) {
            return;
        }
        $notificationLog = NotificationLog::create([
            'user_id' => $recipient->id,
            'type' => $type,
            'message' => $body,
            'url' => $url,
        ]);
        if (!$recipient->fcm_token) {
            return;
        }

        $separator = str_contains($url, '?') ? '&' : '?';
        $urlWithId = $url . $separator . 'notification_id=' . $notificationLog->id;

        $message = CloudMessage::new()
            ->withData([
                'title' => $title,
                'body' => $body,
                'url' => $urlWithId,
            ]);
        try {
            $messaging->send($message->toToken($recipient->fcm_token));
        } catch (NotFound $e) {
            Log::warning("Token FCM tidak valid untuk user {$recipient->id}. Menghapus token.");
            $recipient->update(['fcm_token' => null]);
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim notifikasi push tipe '{$type}' ke user {$recipient->id}: " . $e->getMessage());
        }
    }

    /**
     * @param Messaging $messaging
     * @param Collection<int, User> $recipients
     * @param string $title
     * @param string $body
     * @param string $url
     * @param string $type
     */
    protected function sendMulticastNotification(
        Messaging $messaging,
        Collection $recipients,
        string $title,
        string $body,
        string $url,
        string $type
    ): void {
        if ($recipients->isEmpty()) {
            return;
        }
        foreach ($recipients as $recipient) {
            NotificationLog::create([
                'user_id' => $recipient->id,
                'type' => $type,
                'message' => $body,
                'url' => $url,
            ]);
        }
        $tokens = $recipients->pluck('fcm_token')->toArray();
        if (empty($tokens)) {
            Log::info("Tidak ada token FCM yang valid untuk dikirimi notifikasi multicast tipe '{$type}'.");
            return;
        }
        $message = CloudMessage::new()
            ->withNotification(FirebaseNotification::create($title, $body))
            ->withData(['url' => $url]);
        try {
            $messaging->sendMulticast($message, $tokens);
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim notifikasi perbaikan berkas: " . $e->getMessage());
        }
    }

    /**
     * @param Messaging $messaging
     * @param Collection<int, User> $recipients
     * @param string $title
     * @param string $body
     * @param string $url
     * @param string $type
     */
    protected function sendBatchNotification(
        Messaging $messaging,
        Collection $recipients,
        string $title,
        string $body,
        string $url,
        string $type
    ): void {
        if ($recipients->isEmpty()) {
            return;
        }

        $messages = [];

        foreach ($recipients as $recipient) {
            // Lewati kalau user gak punya token
            if (!$recipient->fcm_token) {
                continue;
            }

            // 1. Buat log di database untuk dapatkan ID unik
            $notificationLog = NotificationLog::create([
                'user_id' => $recipient->id,
                'type' => $type,
                'message' => $body,
                'url' => $url,
            ]);

            // 2. Bikin URL unik khusus untuk user ini
            $separator = str_contains($url, '?') ? '&' : '?';
            $uniqueUrl = $url . $separator . 'notification_id=' . $notificationLog->id;

            // 3. Masukkan ke dalam array paket pesan
            // Perhatikan penggunaan ->withTarget('token', ...)
            $messages[] = CloudMessage::new()
                ->withTarget('token', $recipient->fcm_token)
                ->withData([
                    'title' => $title,
                    'body'  => $body,
                    'url'   => $uniqueUrl,
                ]);
        }

        if (empty($messages)) {
            Log::info("Tidak ada token FCM yang valid untuk dikirimi notifikasi batch tipe '{$type}'.");
            return;
        }

        // 🚨 ATURAN FIREBASE: Maksimal 500 pesan dalam 1x hit API batch.
        // Kalau target user lu ribuan, kita harus pecah (chunk) array-nya per 500.
        $chunks = array_chunk($messages, 500);

        foreach ($chunks as $chunk) {
            try {
                // Inilah keajaibannya: CUMA 1x HIT API ke Firebase untuk 500 user sekaligus!
                $messaging->sendAll($chunk);
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim notifikasi batch tipe '{$type}': " . $e->getMessage());
            }
        }
    }
}
