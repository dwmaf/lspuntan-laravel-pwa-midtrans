<?php

namespace App\Notifications;

use App\Models\Asesi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PendaftarBaru extends Notification
{
    use Queueable;

    protected $asesi;

    /**
     * Buat instance notifikasi baru.
     */
    public function __construct(Asesi $asesi)
    {
        $this->asesi = $asesi;
    }

    /**
     * Tentukan channel pengiriman notifikasi.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Kita hanya butuh notifikasi in-app (database)
    }

    /**
     * Ubah notifikasi menjadi array untuk disimpan di database.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Pendaftar baru untuk sertifikasi: ' . $this->asesi->sertification->skema->nama_skema,
            'link' => route('admin.applicants.show', $this->asesi->id), // Link menuju rincian pendaftar
            'asesi_name' => $this->asesi->student->name,
        ];
    }
}