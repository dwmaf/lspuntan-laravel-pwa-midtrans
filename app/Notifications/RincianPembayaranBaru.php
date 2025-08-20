<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RincianPembayaranBaru extends Notification
{
    use Queueable;
    protected $sertification;

    public function __construct($sertification)
    {
        $this->sertification = $sertification;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Rincian pembayaran baru untuk sertifikasi ' . $this->sertification->skema->nama_skema,
            'link' => route('asesi.sertifikasi.rincian', $this->sertification->id),
        ];
    }
}