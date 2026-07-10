<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\NotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Exception\Messaging\NotFound;

class SendPushNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
        public string $title,
        public string $body,
        public string $url,
        public string $type,
    ) {}

    public function handle(Messaging $messaging): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            Log::warning("User {$this->userId} tidak ditemukan saat mengirim push notif.");
            return;
        }

        $notificationLog = NotificationLog::create([
            'user_id' => $user->id,
            'type' => $this->type,
            'message' => $this->body,
            'url' => $this->url,
        ]);

        if (!$user->fcm_token) {
            return;
        }

        $separator = str_contains($this->url, '?') ? '&' : '?';
        $urlWithId = $this->url . $separator . 'notification_id=' . $notificationLog->id;

        $message = CloudMessage::new()
            ->withNotification(FirebaseNotification::create($this->title, $this->body))
            ->withData([
                'url' => $urlWithId,
            ]);

        try {
            $messaging->send($message->toToken($user->fcm_token));
        } catch (NotFound $e) {
            Log::warning("Token FCM tidak valid untuk user {$user->id}. Menghapus token.");
            $user->update(['fcm_token' => null]);
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim notifikasi push tipe '{$this->type}' ke user {$user->id}: " . $e->getMessage());
        }
    }
}
