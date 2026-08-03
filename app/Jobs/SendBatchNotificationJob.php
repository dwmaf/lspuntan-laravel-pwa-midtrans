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

class SendBatchNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $notifications,
        public string $type,
    ) {}

    public function handle(Messaging $messaging): void
    {
        $userIds = array_column($this->notifications, 'user_id');
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        if ($users->isEmpty()) {
            return;
        }

        $messages = [];

        foreach ($this->notifications as $notif) {
            $user = $users->get($notif['user_id']);

            if (!$user) {
                continue;
            }

            $notificationLog = NotificationLog::create([
                'user_id' => $user->id,
                'type' => $this->type,
                'message' => $notif['body'],
                'url' => $notif['url'],
            ]);

            if ($user->fcm_token) {
                $separator = str_contains($notif['url'], '?') ? '&' : '?';
                $uniqueUrl = $notif['url'] . $separator . 'notification_id=' . $notificationLog->id;
    
                $messages[] = CloudMessage::new()
                    ->toToken($user->fcm_token)
                    ->withData([
                        'title' => $notif['title'],
                        'body'  => $notif['body'],
                        'url'   => $uniqueUrl,
                    ]);
            }
        }

        if (empty($messages)) {
            Log::info("Tidak ada token FCM valid untuk batch tipe '{$this->type}'. (Namun In-App Notif tetap masuk).");
            return;
        }

        $chunks = array_chunk($messages, 500);

        foreach ($chunks as $chunk) {
            try {
                $messaging->sendAll($chunk);
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim batch notif tipe '{$this->type}': " . $e->getMessage());
            }
        }
    }
}
