<?php
//ketika admin update status asesi
namespace App\Notifications;

use App\Models\Asesi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class StatusAsesiUpdated extends Notification
{
    use Queueable;
    protected $sert_id;
    protected $asesi_id;
    protected $status;

    public function __construct($sert_id, $asesi_id, $status)
    {
        $this->sert_id = $sert_id;
        $this->asesi_id = $asesi_id;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $notificationId = $this->id;
        return [
            'message' => 'Status asesmen Anda diubah menjadi: ' . $this->status,
            //tujuan functionnya ada di KelolaSertifikasiAsesiController, function detail_applied_sertifikasi
            'link' => route('asesi.sertifikasi.applied.show', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId]),
        ];
    }
}