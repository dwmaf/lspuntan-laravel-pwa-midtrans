<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengumumanBaru extends Notification
{
    use Queueable;
    protected $pengumuman;

    public function __construct($pengumuman)
    {
        $this->pengumuman = $pengumuman;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Pengumuman baru: ' . $this->pengumuman->judul,
            'link' => route('asesi.pengumuman.show', $this->pengumuman->id),
        ];
    }
}