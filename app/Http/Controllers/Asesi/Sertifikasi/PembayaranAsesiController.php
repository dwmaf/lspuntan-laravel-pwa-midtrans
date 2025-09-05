<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Illuminate\Support\Facades\Storage;
use App\Notifications\AsesiUploadBuktiPembayaran;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Notification;

class PembayaranAsesiController extends Controller
{

    // buat nampilin daftar sertifikasi yg tersedia di sisi asesi
    public function index_rincian_pembayaran($sert_id, $asesi_id,  Request $request)
    {
        // dd($request);
        NotificationController::markAsRead($request);
        return view('asesi.sertifikasi.bayar.indexbayar', [
            'sertification' => Sertification::with('asesor', 'skema','pembuatrincianpembayaran.asesor')->find($sert_id),
            'asesi' => Asesi::with('student')->find($asesi_id)
        ]);
    }

    public function upload_bukti_pembayaran($sert_id, $asesi_id, Request $request)
    {
        // dd($request);
        $request->validate([
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ]);
        // $sertification = Sertification::with('asesor', 'skema')->find($sert_id);
        // $asesi = Asesi::with('student.user')->find($asesi_id);
        $transaction = Transaction::firstOrCreate(
            ['asesi_id' => $asesi_id, 'sertification_id' => $sert_id],
            ['status' => 'pending', 'tipe' => 'manual']
        );
        if ($request->hasFile('bukti_bayar')) {
            if ($transaction->bukti_bayar && Storage::disk('public')->exists($transaction->bukti_bayar)) {
                Storage::disk('public')->delete($transaction->bukti_bayar);
            }
            $fileData = FileHelper::storeFileWithUniqueName($request->file('bukti_bayar'), 'bukti_bayar');
            $transaction->bukti_bayar = $fileData['path'];
        }
        $transaction['status'] = 'pending';
        $transaction->save();

        $admins = User::role('admin')->get();
        Notification::send($admins, new AsesiUploadBuktiPembayaran($sert_id, $asesi_id));
        
        return redirect()->back()->with('success', 'Berhasil upload bukti pembayaran, admin akan memverifikasinya.');
    }

}
