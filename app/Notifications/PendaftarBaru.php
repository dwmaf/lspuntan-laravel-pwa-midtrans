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

    protected $asesi_id;
    protected $sertification_id;
    protected $nama_skema;
    protected $name;

    /**
     * Buat instance notifikasi baru.
     */
    public function __construct($name, $asesi_id, $sertification_id, $nama_skema)
    {
        $this->asesi_id = $asesi_id;
        $this->sertification_id = $sertification_id;
        $this->nama_skema = $nama_skema;
        $this->name = $name;
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
            'message' => $this->name.' mendaftar untuk sertifikasi ' . $this->nama_skema,
            //tujuan functionnya ada di PendaftarController, function rincian_data_asesi
            'link' => route('admin.sertifikasi.pendaftar.show', [$this->sertification_id, $this->asesi_id, 'notification_id' => $notificationId]),
        ];
    }

}