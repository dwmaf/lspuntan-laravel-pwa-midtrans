<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\NotificationLog;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Illuminate\Support\Facades\Storage;
use App\Notifications\AsesiUploadBuktiPembayaran;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;

class PembayaranAsesiController extends Controller
{

    // buat nampilin daftar sertifikasi yg tersedia di sisi asesi
    public function index_rincian_pembayaran($sert_id, $asesi_id,  Request $request)
    {
        // dd($request);
        NotificationController::markAsRead($request);
        $asesi = Asesi::with([
            'student',
            'transaction' => fn($q) => $q->latest(), // Ambil semua transaksi, urutkan terbaru
        ])->findOrFail($asesi_id);

        // Pastikan asesi ini milik user yang sedang login
        if ($asesi->student->user_id !== $request->user()->id) {
            abort(403);
        }

        // Siapkan transaksi terakhir untuk kemudahan di frontend
        $asesi->latest_transaction = $asesi->transaction->first();
        return Inertia::render('Asesi/PembayaranAsesi', [
            'sertification' => Sertification::with('skema', 'pembuatrincianpembayaran')->findOrFail($sert_id),
            'asesi' => $asesi,
        ]);
    }

    public function upload_bukti_pembayaran($sert_id, $asesi_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $request->validate([
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        // $sertification = Sertification::with('asesor', 'skema')->find($sert_id);
        $asesi = Asesi::with('student.user')->find($asesi_id);
        $transaction = Transaction::firstOrNew(
            ['asesi_id' => $asesi_id, 'sertification_id' => $sert_id],
            ['status' => 'pending', 'tipe' => 'manual']
        );
        if ($request->has('delete_files')) {
            foreach ($request->delete_files as $fieldName) {
                if ($transaction->$fieldName) {
                    Storage::disk('public')->delete($transaction->$fieldName);
                    $transaction->$fieldName = null;
                }
            }
        }
        if ($request->hasFile('bukti_bayar')) {
            if ($transaction->bukti_bayar && Storage::disk('public')->exists($transaction->bukti_bayar)) {
                Storage::disk('public')->delete($transaction->bukti_bayar);
            }
            $fileData = FileHelper::storeFileWithUniqueName($request->file('bukti_bayar'), 'bukti_bayar');
            $transaction->bukti_bayar = $fileData['path'];
        }
        $transaction['status'] = 'pending';
        $transaction->save();

        $recipients = User::role('admin')->get();
        $sertification = Sertification::with('skema')->findOrFail($sert_id);
        if ($recipients->isNotEmpty()) {
            $body = $asesi->student->user->name . ' mengunggah bukti pembayaran untuk sertifikasi ' . $sertification->skema->nama_skema;
            $url = route('admin.sertifikasi.pendaftar.show', ['sert_id' => $sertification->id, 'asesi_id' => $asesi->id]);
            foreach ($recipients as $recipient) {
                NotificationLog::create([
                    'user_id' => $recipient->id,
                    'type' => 'AsesiUploadBuktiPembayaran',
                    'message' => $body,
                    'link' => $url,
                ]);
            }
            $pushRecipients = $recipients->whereNotNull('fcm_token');
            if ($pushRecipients->isNotEmpty()) {
                $tokens = $pushRecipients->pluck('fcm_token')->toArray();
                $message = CloudMessage::new()
                    ->withNotification(FirebaseNotification::create($body))
                    ->withData(['url' => $url]);
                try {
                    $report = $messaging->sendMulticast($message, $tokens);
                    if ($report->hasFailures()) {
                        $invalidTokens = $report->invalidTokens();
                        if (!empty($invalidTokens)) {
                            Log::warning('Menghapus token FCM yang tidak valid/kedaluwarsa.', ['tokens' => $invalidTokens]);
                            User::whereIn('fcm_token', $invalidTokens)->update(['fcm_token' => null]);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error("Gagal mengirim multicast push notification: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('message', 'Berhasil upload bukti pembayaran, admin akan memverifikasinya.');
    }
}
