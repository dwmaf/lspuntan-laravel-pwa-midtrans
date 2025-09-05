<?php
//ketika asesi ngunggah bukti pembayaran
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

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
        return ['database', FcmChannel::class];
    }

    public function toArray($notifiable)
    {
        $notificationId = $this->id;
        return [
            'message' => 'Asesi mengunggah bukti pembayaran.',
            //tujuan functionnya ada di PendaftarController, function rincian_data_asesi
            'link' => route('admin.sertifikasi.pendaftar.show', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId]),
        ];
    }

    public function toFcm($notifiable)
    {
        $notificationId = $this->id;
        return (new FcmMessage(notification: new FcmNotification(
            title: 'Pembayaran Baru Diterima',
            body: 'Seorang asesi telah mengunggah bukti pembayaran. Silakan periksa.',
            // Anda bisa menambahkan URL gambar logo di sini
            image: asset('logo-lsp.png')
        )))
            // PERBAIKAN 2: Pastikan link di data menyertakan notification_id
            ->data([
                'link' => route('admin.sertifikasi.pendaftar.show', [$this->sert_id, $this->asesi_id, 'notification_id' => $notificationId])
            ]);
    }
    /*
    * @param  mixed  $notifiable
     * @return \NotificationChannels\Fonnte\FonnteMessage
     */
    // public function toFonnte($notifiable)
    // {
    //     // Anda bisa mengirim pesan biasa (jika membalas) atau template.
    //     // Untuk notifikasi, kita gunakan pesan biasa yang lebih fleksibel jika diizinkan.
    //     // Jika harus template, gunakan ->template('nama_template', ['var1', 'var2'])
        
    //     $url = route('admin.sertifikasi.pendaftar.show', [$this->sert_id, $this->asesi_id]);

    //     return FonnteMessage::create()
    //         ->content(
    //             "🔔 *Notifikasi Pembayaran Baru* 🔔\n\n" .
    //             "Halo Admin,\n\n" .
    //             "Asesi atas nama *{$this->asesiName}* telah mengunggah bukti pembayaran baru.\n\n" .
    //             "Silakan periksa detailnya pada link di bawah ini:\n" .
    //             $url . "\n\n" .
    //             "Terima kasih."
    //         );
    // }
}
