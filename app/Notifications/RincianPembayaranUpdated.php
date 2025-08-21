<?php
//ketika admin buat rincian pembayaran
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Sertification;
use App\Models\Asesi;
use Illuminate\Notifications\Notification;

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
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Rincian pembayaran diupdate untuk sertifikasi ' . $this->sertification->skema->nama_skema,
            'link' => route('asesi.applied.payment.create', [$this->sertification->id, $this->asesi->id]),
        ];
    }
}