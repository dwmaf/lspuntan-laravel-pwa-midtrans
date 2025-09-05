<?php
//ketika admin ngunggah sertifikat ke asesi
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class AsesiUploadTugasAsesmen extends Notification
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
            'message' => 'Asesi mengunggah tugas asesmennya.',
            //tujuan functionnya ada di AsesmenController, function rincian_asesmen_asesi
            'link' => route('admin.sertifikasi.rincian.assessment.asesi.index', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId]),
        ];
    }

    public function toFcm($notifiable)
    {
        $notificationId = $this->id;
        return (new FcmMessage(notification: new FcmNotification(
            title: 'Asesi mengunggah tugas asesmennya.',
            body: 'Seorang asesi telah mengunggah tugas asesmennya. Silakan periksa.',
            image: asset('logo-lsp.png')
        )))
            ->data([
                'link' => route('admin.sertifikasi.pendaftar.show', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId])
            ]);
    }
}