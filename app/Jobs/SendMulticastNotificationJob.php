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

class SendMulticastNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $userIds,
        public string $title,
        public string $body,
        public string $url,
        public string $type,
    ) {}

    public function handle(Messaging $messaging): void
    {
        $users = User::whereIn('id', $this->userIds)->get();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            NotificationLog::create([
                'user_id' => $user->id,
                'type' => $this->type,
                'message' => $this->body,
                'url' => $this->url,
            ]);
        }

        $tokens = $users->pluck('fcm_token')->filter()->values()->toArray();

        if (empty($tokens)) {
            Log::info("Tidak ada token FCM yang valid untuk multicast tipe '{$this->type}'.");
            return;
        }

        $message = CloudMessage::new()
            ->withNotification(FirebaseNotification::create($this->title, $this->body))
            ->withData(['url' => $this->url]);

        try {
            $messaging->sendMulticast($message, $tokens);
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim multicast tipe '{$this->type}': " . $e->getMessage());
        }
    }
}
