<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentInstruction;
use App\Models\Asesi;
use App\Models\NotificationLog;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;

class SendPaymentReminders extends Command
{
    protected $signature = 'reminders:payment';
    protected $description = 'Kirim notifikasi pengingat H-1 sebelum deadline pembayaran';

    public function handle(Messaging $messaging)
    {
        $this->info('Mulai mengirim pengingat pembayaran...');

        // 1. Tentukan rentang waktu: cari deadline yang jatuh pada hari "besok"
        $tomorrow = now()->addDay();

        // 2. Ambil semua instruksi pembayaran yang deadline-nya adalah besok
        $instructions = PaymentInstruction::whereDate('deadline', $tomorrow)->get();

        foreach ($instructions as $instruction) {
            // 3. Ambil semua asesi yang relevan (status dilanjutkan) DAN BELUM terverifikasi pembayarannya
            $asesisToSend = Asesi::where('sertification_id', $instruction->sertification_id)
                ->where('status', 'dilanjutkan_asesmen')
                ->whereDoesntHave('transaction', function ($query) {
                    $query->where('status', 'bukti_pembayaran_terverifikasi');
                })
                ->with('student.user', 'sertification.skema')
                ->get();

            if ($asesisToSend->isEmpty()) {
                continue; // Lanjut ke instruksi berikutnya jika tidak ada asesi yang perlu diingatkan
            }

            $this->info("Menemukan {$asesisToSend->count()} asesi untuk sertifikasi #{$instruction->sertification_id}");

            // 4. Loop dan kirim notifikasi ke setiap asesi
            foreach ($asesisToSend as $asesi) {
                $user = $asesi->student->user;
                if ($user && $user->fcm_token) {
                    $body = "Pengingat: Batas waktu pembayaran untuk sertifikasi {$asesi->sertification->skema->nama_skema} adalah besok.";
                    $url = route('asesi.payment.create', ['sert_id' => $asesi->sertification_id, 'asesi_id' => $asesi->id]);

                    $notificationLog = NotificationLog::create([
                        'user_id' => $user->id,
                        'type' => 'PaymentReminder',
                        'message' => $body,
                        'link' => $url,
                    ]);

                    $urlWithId = $url . '?notification_id=' . $notificationLog->id;
                    $message = CloudMessage::new()
                        ->withNotification(FirebaseNotification::create('Pengingat Pembayaran', $body))
                        ->withData(['url' => $urlWithId]);

                    try {
                        $messaging->send($message->toToken($user->fcm_token));
                        $this->line("-> Mengirim pengingat ke {$user->name}");
                    } catch (\Throwable $e) {
                        Log::error("Gagal mengirim pengingat pembayaran ke user {$user->id}: " . $e->getMessage());
                    }
                }
            }
        }

        $this->info('Selesai mengirim pengingat pembayaran.');
        return Command::SUCCESS;
    }
}