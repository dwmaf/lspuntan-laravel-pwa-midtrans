<?php

namespace App\Traits;

use App\Jobs\SendMulticastNotificationJob;
use App\Jobs\SendPushNotificationJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait SendsPushNotifications
{
    protected function sendPushNotification(
        ?User $recipient,
        string $title,
        string $body,
        string $url,
        string $type
    ): void {
        if (!$recipient) {
            return;
        }

        SendPushNotificationJob::dispatch(
            $recipient->id,
            $title,
            $body,
            $url,
            $type,
        );
    }

    protected function sendMulticastNotification(
        Collection $recipients,
        string $title,
        string $body,
        string $url,
        string $type
    ): void {
        if ($recipients->isEmpty()) {
            return;
        }

        SendMulticastNotificationJob::dispatch(
            $recipients->pluck('id')->toArray(),
            $title,
            $body,
            $url,
            $type,
        );
    }
}
