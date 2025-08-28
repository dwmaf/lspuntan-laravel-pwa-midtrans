<?php
//ketika admin ngunggah sertifikat ke asesi
namespace App\Notifications;

use App\Models\Sertifikat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AsesiUploadBuktiPembayaran extends Notification
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
            'message' => 'Asesi mengunggah bukti pembayaran.',
            'link' => route('admin.sertifikasi.pendaftar.show', [$this->sert_id, $this->asesi_id]),
        ];
    }
}