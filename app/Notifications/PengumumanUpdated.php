<?php
//ketika admin buat pengumuman
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

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
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Pengumuman diperbaharui: ',
            'link' => route('asesi.pengumuman.index', [$this->sert_id, $this->asesi_id]),
        ];
    }
}