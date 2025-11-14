<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Message;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\RegistrationTokens;
use Kreait\Firebase\Messaging\MessageTarget;
use Kreait\Firebase\Messaging\SendReport;

/**
 * Service palsu untuk menggantikan Firebase Messaging di lingkungan non-produksi.
 * Metode-metodenya hanya akan mencatat ke log alih-alih mengirim notifikasi sungguhan.
 */
class FakeMessagingService implements Messaging
{
    public function send($message, bool $validateOnly = false): array
    {
        Log::info('[FAKE FCM] Mengirim 1 pesan (send).', ['message' => $this->messageToArray($message)]);
        return ['status' => 'ok', 'message' => 'Faked single send'];
    }

    public function sendMulticast($message, $deviceTokens, bool $validateOnly = false): \Kreait\Firebase\Messaging\MulticastSendReport
    {
        $count = count($deviceTokens);
        Log::info("[FAKE FCM] Mengirim {$count} pesan (multicast).", ['message' => $this->messageToArray($message)]);

        // --- AWAL PERUBAHAN ---
        // Buat laporan palsu untuk setiap token
        $reports = [];
        foreach ($deviceTokens as $token) {
            $target = MessageTarget::with(MessageTarget::TOKEN, $token);
            // Buat laporan 'sukses' palsu
            $reports[] = SendReport::success($target, [], $message);
        }

        // Buat MulticastSendReport dengan cara yang benar
        return \Kreait\Firebase\Messaging\MulticastSendReport::withItems($reports);
        // --- AKHIR PERUBAHAN ---
    }

    public function sendAll($messages, bool $validateOnly = false): \Kreait\Firebase\Messaging\MulticastSendReport
    {
        $count = count($messages);
        Log::info("[FAKE FCM] Mengirim {$count} pesan (sendAll).");

        // --- AWAL PERUBAHAN ---
        // Cukup kembalikan laporan kosong karena kita tidak punya target spesifik
        return \Kreait\Firebase\Messaging\MulticastSendReport::withItems([]);
        // --- AKHIR PERUBAHAN ---
    }

    // Metode lain yang dibutuhkan oleh interface, bisa dibiarkan kosong atau log saja.
    public function validate($message): array { return []; }
    public function getAppInstance($appInstanceId): \Kreait\Firebase\Messaging\AppInstance { throw new \Exception('Not implemented'); }
    /**
     * @param \Kreait\Firebase\Messaging\RegistrationTokens|\Kreait\Firebase\Messaging\RegistrationToken|array<int, \Kreait\Firebase\Messaging\RegistrationToken|string>|string $tokens
     * @return array{valid: list<string>, unknown: list<string>, invalid: list<string>}
     */
    public function validateRegistrationTokens($tokens): array
    {
        $count = count((array) $tokens);
        Log::info("[FAKE FCM] Memvalidasi {$count} token (validateRegistrationTokens).");
        
        // Kembalikan array dengan struktur yang benar, tetapi kosong.
        return [
            'valid' => [],
            'unknown' => [],
            'invalid' => [],
        ];
    }

    public function subscribeToTopic($topic, $deviceTokens): array
    {
        $count = count((array) $deviceTokens);
        Log::info("[FAKE FCM] Subscribe {$count} token ke topik '{$topic}'.");
        return [];
    }

    // --- AWAL PERUBAHAN ---
    public function subscribeToTopics(iterable $topics, $registrationTokenOrTokens): array
    {
        $count = count((array) $registrationTokenOrTokens);
        Log::info("[FAKE FCM] Subscribe {$count} target ke beberapa topik.");
        return [];
    }
    // --- AKHIR PERUBAHAN ---

    public function unsubscribeFromTopic($topic, $deviceTokens): array
    {
        $count = count((array) $deviceTokens);
        Log::info("[FAKE FCM] Unsubscribe {$count} token dari topik '{$topic}'.");
        return [];
    }

    // --- AWAL PERUBAHAN ---
    public function unsubscribeFromTopics(array $topics, $registrationTokenOrTokens): array
    {
        $count = count((array) $registrationTokenOrTokens);
        Log::info("[FAKE FCM] Unsubscribe {$count} target dari beberapa topik.");
        return [];
    }
    // --- AKHIR PERUBAHAN ---

    public function unsubscribeFromAllTopics($registrationTokenOrTokens): array
    {
        $count = count((array) $registrationTokenOrTokens);
        Log::info("[FAKE FCM] Unsubscribe {$count} target dari semua topik.");
        return [];
    }
    private function messageToArray($message): array
    {
        if ($message instanceof CloudMessage) {
            return $message->jsonSerialize();
        }
        return (array) $message;
    }
}