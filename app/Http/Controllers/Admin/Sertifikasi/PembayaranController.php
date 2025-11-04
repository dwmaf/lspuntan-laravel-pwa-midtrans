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
            'sertification' => Sertification::with('paymentInstruction')->find($sert_id)
        ]);
    }

    public function update_rincian_pembayaran($sert_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'deadline' => 'required|date',
            'biaya' => 'required|numeric|min:0',
            'is_published' => 'required|boolean'
        ]);

        $sertification = Sertification::with('skema')->findOrFail($sert_id);
        $sertification->paymentInstruction()->updateOrCreate(
            ['sertification_id' => $sertification->id],
            [
                'content' => $validatedData['content'],
                'biaya' => $validatedData['biaya'],
                'deadline' => $validatedData['deadline'],
                'user_id' => $request->user()->id,
                'published_at' => $request->boolean('is_published') ? now() : null,
            ]
        );

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
                if ($user->fcm_token) {
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
