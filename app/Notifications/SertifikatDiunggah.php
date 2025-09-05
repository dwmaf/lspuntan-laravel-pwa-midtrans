<?php
//ketika admin ngunggah sertifikat ke asesi
namespace App\Notifications;

use App\Models\Sertifikat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class SertifikatDiunggah extends Notification
{
    use Queueable;
    protected $sert_id;
    protected $asesi_id;

    public function __construct($sert_id, $asesi_id)
    {
        $this->sert_id = $sert_id;
        $this->asesi_id = $asesi_id;
    }

    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    public function toArray($notifiable)
    {
        $notificationId = $this->id; 
        return [
            'message' => 'Sertifikat Anda telah diunggah.',
            //tujuan functionnya ada di KelolaSertifikasiAsesiController, function detail_applied_sertifikasi
            'link' => route('asesi.sertifikasi.applied.show', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId]),
        ];
    }

    public function toFcm($notifiable)
    {
        $notificationId = $this->id;
        return (new FcmMessage(notification: new FcmNotification(
            title: 'Sertifikat Anda telah diunggah.',
            image: asset('logo-lsp.png')
        )))
            ->data([
                'link' => route('asesi.sertifikasi.applied.show', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId])
            ]);
    }
}