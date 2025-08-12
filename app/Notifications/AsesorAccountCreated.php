<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;
// implements ShouldQueue
class AsesorAccountCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Buat token reset password untuk user baru
        /** @var \Illuminate\Auth\Passwords\PasswordBroker $passwordBroker */
        $passwordBroker = Password::broker();
        $token = $passwordBroker->createToken($notifiable);

        // Buat URL untuk halaman set/reset password
        $url = route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
                    ->subject('Akun Asesor Anda Telah Dibuat')
                    ->line('Selamat datang! Akun asesor Anda di platform LSP UNTAN telah berhasil dibuat oleh admin.')
                    ->line('Silakan klik tombol di bawah ini untuk mengatur password Anda dan mengaktifkan akun Anda.')
                    ->action('Atur Password Anda', $url)
                    ->line('Jika Anda tidak merasa membuat akun ini, Anda dapat mengabaikan email ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
