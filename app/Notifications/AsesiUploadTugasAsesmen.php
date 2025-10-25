<?php
//ketika admin ngunggah sertifikat ke asesi
//lets discuss ini perlu atau tidak utk yg push notif
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
    protected $body;

    public function __construct($sert_id, $asesi_id, $body)
    {
        $this->sert_id = $sert_id;
        $this->asesi_id = $asesi_id;
        $this->body = $body;
    }
    /**
     * Dapatkan channel notifikasi.
     * Kita tidak lagi menggunakan channel 'database' bawaan.
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return [];
    }

    public function getData(): array
    {
        return [
            'message' => $this->body,
            'link' => route('admin.sertifikasi.rincian.assessment.asesi.index', [$this->sert_id, $this->asesi_id]),
        ];
    }
}