<?php
//ketika admin update status asesi
namespace App\Notifications;

use App\Models\Asesi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

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
        return [
            'message' => 'Status asesmen Anda diubah menjadi: ' . $this->status,
            'link' => route('asesi.sertifikasi.applied.show', [$this->sert_id, $this->asesi_id]),
        ];
    }
}