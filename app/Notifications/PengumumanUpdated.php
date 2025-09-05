<?php
//ketika admin buat pengumuman
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class PengumumanUpdated extends Notification
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
            'message' => 'Pengumuman diperbaharui: ',
            //tujuan functionnya ada di PengumumanAsesiController, function index_pengumuman_asesi
            'link' => route('asesi.pengumuman.index', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId]),
        ];
    }

    public function toFcm($notifiable)
    {
        $notificationId = $this->id;
        return (new FcmMessage(notification: new FcmNotification(
            title: 'Pengumuman diperbaharui',
            image: asset('logo-lsp.png')
        )))
            ->data([
                'link' => route('asesi.pengumuman.index', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId])
            ]);
    }
}