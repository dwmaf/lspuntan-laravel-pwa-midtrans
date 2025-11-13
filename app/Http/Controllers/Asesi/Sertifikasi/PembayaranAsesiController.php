<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Traits\SendsPushNotifications;
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
use SendsPushNotifications;
    
    public function index_rincian_pembayaran($sert_id, $asesi_id,  Request $request)
    {
        // dd($request);
        NotificationController::markAsRead($request);
        $asesi = Asesi::with([
            'student',
            'transaction' => fn($q) => $q->latest(), // Ambil semua transaksi, urutkan terbaru
        ])->findOrFail($asesi_id);
        
        if ($asesi->student->user_id !== $request->user()->id) {
            abort(403);
        }

        $asesi->latest_transaction = $asesi->transaction->first();
        return Inertia::render('Asesi/PembayaranAsesi', [
            'sertification' => Sertification::with('skema', 'paymentInstruction')->findOrFail($sert_id),
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
        FileHelper::handleSingleFileDeletes($transaction, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($transaction, ['bukti_bayar'], $request, 'bukti_bayar');
        $transaction['status'] = 'pending';
        $transaction->save();

        $recipients = User::role('admin')->get();
        $sertification = Sertification::with('skema')->findOrFail($sert_id);
        if ($recipients->isNotEmpty()) {
            $title = 'Bukti Pembayaran Baru';
            $body = $asesi->student->user->name . ' mengunggah bukti pembayaran untuk sertifikasi ' . $sertification->skema->nama_skema;
            $url = route('admin.sertifikasi.pendaftar.show', ['sert_id' => $sertification->id, 'asesi_id' => $asesi->id]);
            foreach ($recipients as $recipient) {
                $this->sendPushNotification($messaging, $recipient, $title, $body, $url, 'AsesiUploadBuktiPembayaran');
            }
        }

        return redirect()->back()->with('message', 'Berhasil upload bukti pembayaran, admin akan memverifikasinya.');
    }
}
