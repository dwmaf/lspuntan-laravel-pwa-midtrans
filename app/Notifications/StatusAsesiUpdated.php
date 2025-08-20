<?php

namespace App\Notifications;

use App\Models\Asesi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusAsesiUpdated extends Notification
{
    use Queueable;
    protected $asesi;
    protected $status;

    public function __construct(Asesi $asesi, $status)
    {
        $this->asesi = $asesi;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Status asesmen Anda diubah menjadi: ' . $this->status,
            'link' => route('asesi.sertifikasi.rincian', $this->asesi->id),
        ];
    }
}