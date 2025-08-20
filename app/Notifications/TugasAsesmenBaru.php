<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TugasAsesmenBaru extends Notification
{
    use Queueable;
    protected $tugas;

    public function __construct($tugas)
    {
        $this->tugas = $tugas;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Tugas asesmen baru: ' . $this->tugas->judul,
            'link' => route('asesi.tugas.show', $this->tugas->id),
        ];
    }
}