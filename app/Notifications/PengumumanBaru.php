<?php
//ketika admin buat pengumuman
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengumumanBaru extends Notification
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
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $notificationId = $this->id; 
        return [
            'message' => 'Pengumuman baru: ',
            //tujuan functionnya ada di PengumumanAsesiController, function index_pengumuman_asesi
            'link' => route('asesi.pengumuman.index', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId]),
        ];
    }
    
}