<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\User;
use App\Models\Asesi;
use App\Models\NotificationLog;
use App\Notifications\RincianPembayaranUpdated;
use App\Traits\SendsPushNotifications;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;

class PembayaranController extends Controller
{
use SendsPushNotifications;
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
            $title = 'Update Rincian Pembayaran';
            $body = 'Rincian pembayaran diupdate untuk sertifikasi ' . $sertification->skema->nama_skema;
            foreach ($asesis as $asesi) {
                $user = $asesi->student->user ?? null;
                $url = route('asesi.payment.create', ['sert_id' => $sertification->id, 'asesi_id' => $asesi->id]);
                $this->sendPushNotification($messaging, $user, $title, $body, $url, 'RincianPembayaranUpdated');
            }
        }
        return redirect()->back()->with('message', 'Rincian pembayaran berhasil disimpan!');
    }
}
