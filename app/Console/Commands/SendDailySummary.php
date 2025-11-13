<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Asesi;
use App\Models\Transaction;
use App\Models\NotificationLog;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;

class SendDailySummary extends Command
{
    protected $signature = 'summary:daily';
    protected $description = 'Kirim ringkasan harian ke semua admin';

    public function handle(Messaging $messaging)
    {
        $this->info('Mulai membuat ringkasan harian...');

        // 1. Kumpulkan data
        $newApplicantsCount = Asesi::whereDate('created_at', today())->count();
        $paymentsToVerifyCount = Transaction::where('status', 'pending')->count();

        // 2. Jika tidak ada yang perlu dilaporkan, berhenti
        if ($newApplicantsCount === 0 && $paymentsToVerifyCount === 0) {
            $this->info('Tidak ada aktivitas baru untuk dilaporkan. Selesai.');
            return Command::SUCCESS;
        }

        // 3. Buat pesan notifikasi
        $messages = [];
        if ($newApplicantsCount > 0) {
            $messages[] = "{$newApplicantsCount} pendaftar baru";
        }
        if ($paymentsToVerifyCount > 0) {
            $messages[] = "{$paymentsToVerifyCount} pembayaran perlu diverifikasi";
        }
        $body = "Ringkasan hari ini: " . implode(', ', $messages) . ".";

        // 4. Ambil semua admin yang memiliki FCM token
        $admins = User::role('admin')->whereNotNull('fcm_token')->get();

        if ($admins->isEmpty()) {
            $this->warn('Tidak ada admin dengan FCM token untuk dikirimi ringkasan.');
            return Command::SUCCESS;
        }

        // 5. Kirim notifikasi menggunakan multicast
        $url = route('admin.dashboard'); // Arahkan ke dashboard admin
        $tokens = $admins->pluck('fcm_token')->toArray();

        // Buat log untuk setiap admin
        foreach ($admins as $admin) {
            NotificationLog::create([
                'user_id' => $admin->id,
                'type' => 'DailySummary',
                'message' => $body,
                'link' => $url,
            ]);
        }

        $message = CloudMessage::new()
            ->withNotification(FirebaseNotification::create('Ringkasan Harian LSP', $body))
            ->withData(['url' => $url]);

        try {
            $report = $messaging->sendMulticast($message, $tokens);
            $this->info("Ringkasan harian berhasil dikirim ke {$report->successes()->count()} admin.");
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim ringkasan harian: " . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}