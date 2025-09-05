<?php
//ketika admin buat rincian pembayaran
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Sertification;
use App\Models\Asesi;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class RincianPembayaranUpdated extends Notification
{
    use Queueable;

    protected $sertification;
    protected $asesi;

    public function __construct(Sertification $sertification, Asesi $asesi)
    {
        $this->sertification = $sertification;
        $this->asesi = $asesi;
    }

    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    public function toArray($notifiable)
    {
        $notificationId = $this->id; 
        return [
            'message' => 'Rincian pembayaran diupdate untuk sertifikasi ' . $this->sertification->skema->nama_skema,
            //tujuan functionnya ada di PembayaranAsesiController, function index_rincian_pembayaran
            'link' => route('asesi.applied.payment.create', [$this->sertification->id, $this->asesi->id, 'notification_id' => $notificationId]),
        ];
    }

    public function toFcm($notifiable)
    {
        $notificationId = $this->id;
        return (new FcmMessage(notification: new FcmNotification(
            title: 'Rincian pembayaran diupdate untuk sertifikasi',
            image: asset('logo-lsp.png')
        )))
            ->data([
                'link' => route('asesi.applied.payment.create', [$this->sertification->id, $this->asesi->id, 'notification_id' => $notificationId])
            ]);
    }
}