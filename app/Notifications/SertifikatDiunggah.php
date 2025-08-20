<?php

namespace App\Notifications;

use App\Models\Sertifikat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SertifikatDiunggah extends Notification
{
    use Queueable;
    protected $sertifikat;

    public function __construct(Sertifikat $sertifikat)
    {
        $this->sertifikat = $sertifikat;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Sertifikat Anda telah diunggah.',
            'link' => asset('storage/' . $this->sertifikat->file_path),
        ];
    }
}