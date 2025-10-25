<?php
//ketika asesor membuat rincian tugas asesmen
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TugasAsesmenBaru extends Notification
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
            'message' => 'Instruksi Tugas asesmen diperbaharui untuk sertifikasi '. $this->nama_skema,
            //tujuan functionnya ada di AsesmenAsesiController, function index_asesmen_asesi
            'link' => route('asesi.assessmen.index', [$this->sert_id, $this->asesi_id,'notification_id' => $notificationId]),
        ];
    }

}