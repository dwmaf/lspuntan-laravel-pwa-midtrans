<?php
// ketika ada mahasiswa yg mendaftar sertifikasi
namespace App\Notifications;

use App\Models\Asesi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

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
        return ['database'];
    }

    /**
     * Ubah notifikasi menjadi array untuk disimpan di database.
     */
    public function toArray(object $notifiable): array
    {
        $notificationId = $this->id; 
        return [
            'message' => 'Pendaftar baru untuk sertifikasi: ' . $this->asesi->sertification->skema->nama_skema,
            //tujuan functionnya ada di PendaftarController, function rincian_data_asesi
            'link' => route('admin.sertifikasi.pendaftar.show', [$this->asesi->sertification->id, $this->asesi->id, 'notification_id' => $notificationId]), // Link menuju rincian pendaftar
            'asesi_name' => $this->asesi->student->name,
        ];
    }

}