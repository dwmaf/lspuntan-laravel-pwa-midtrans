<?php
//ketika admin buat rincian pembayaran
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Sertification;
use App\Models\Asesi;
use Illuminate\Notifications\Notification;

class RincianPembayaranUpdated extends Notification
{
    use Queueable;

    protected $sert_id;
    protected $asesi_id;
    protected $nama_skema;

    public function __construct($sert_id, $asesi_id, $nama_skema)
    {
        $this->sert_id = $sert_id;
        $this->asesi_id = $asesi_id;
        $this->nama_skema = $nama_skema;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $notificationId = $this->id; 
        return [
            'message' => 'Rincian pembayaran diupdate untuk sertifikasi ' . $this->nama_skema,
            'link' => route('asesi.payment.create', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId]),
        ];
    }

}