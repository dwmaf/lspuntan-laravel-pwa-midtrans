<?php
//ketika asesor membuat rincian tugas asesmen
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TugasAsesmenBaru extends Notification
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
            'message' => 'Tugas asesmen diperbaharui',
            'link' => route('asesi.assessmen.index', [$this->sert_id, $this->asesi_id,]),
        ];
    }
}