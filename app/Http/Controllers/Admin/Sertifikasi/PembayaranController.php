<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\User;
use App\Models\Asesi;
use App\Models\NotificationLog;
use App\Notifications\RincianPembayaranUpdated;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;

class PembayaranController extends Controller
{

    public function index_rincian_pembayaran($sert_id, Request $request)
    {
        return Inertia::render('Admin/PembayaranAdmin', [
            'sertification' => Sertification::with('pembuatrincianpembayaran')->find($sert_id)
        ]);
    }

    public function update_rincian_pembayaran($sert_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $request->validate([
            'rincian_pembayaran' => 'required|string',
            'tgl_bayar_ditutup' => 'required|date',
            'harga' => 'required|numeric|min:0',
        ]);

        $sertification = Sertification::with('skema')->findOrFail($sert_id);
        $sertification->fill($request->only([
            'rincian_pembayaran',
            'tgl_bayar_ditutup',
            'harga',
        ]));
        $sertification->rincian_pembayaran = $request->rincian_pembayaran;
        $sertification->rincianbayar_madeby = $request->user()->id;
        if (is_null($sertification->rincianbayar_createdat)) {
            $sertification->rincianbayar_createdat = now();
        } else {
            $sertification->rincianbayar_updatedat = now();
        }
        $sertification->save();


        $asesis = Asesi::with(['student.user'])
            ->where('sertification_id', $sert_id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        if ($asesis->isNotEmpty()) {
            foreach ($asesis as $asesi) {
                $user = $asesi->student->user ?? null;
                $body = 'Rincian pembayaran diupdate untuk sertifikasi ' . $sertification->skema->nama_skema;
                $url = route('asesi.payment.create', ['sert_id' => $sertification->id, 'asesi_id' => $asesi->id]);
                if ($user) {
                    NotificationLog::create([
                        'user_id' => $user->id,
                        'type' => 'RincianPembayaranUpdated',
                        'message' => $body,
                        'link' => $url,
                    ]);
                }
                if ($user->fcm_token){
                    $message = CloudMessage::new()
                        ->withNotification(FirebaseNotification::create($body))
                        ->withData(['url' => $url]);
                    try {
                        $messaging->send($message->toToken($user->fcm_token));
                    } catch (NotFound $e) {
                        Log::warning("Token FCM tidak valid untuk user {$user->id}. Menghapus token.");
                        $user->update(['fcm_token' => null]);
                    } catch (\Throwable $e) {
                        Log::error("Gagal mengirim notifikasi asesi mendaftar sertifikasi ke user {$user->id}: " . $e->getMessage());
                    }
                }
            }
        }
        return redirect()->back()->with('message', 'Rincian pembayaran berhasil disimpan!');
    }
}
