<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Traits\SendsPushNotifications;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;


class PembayaranController extends Controller
{
    use SendsPushNotifications;
    public function index_rincian_pembayaran(Sertification $sertification, Request $request)
    {
        return Inertia::render('Admin/PembayaranAdmin', [
            'sertification' => $sertification->load('paymentInstruction')
        ]);
    }

    public function update_rincian_pembayaran(Sertification $sertification, Request $request, Messaging $messaging)
    {
        // dd($request);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'deadline_bayar' => 'required|date',
            'biaya' => 'required|numeric|min:0',
            'send_notification' => 'required|boolean'
        ]);

        $sertification->load('skema');
        $instruction = $sertification->paymentInstruction()->firstOrNew([]);
        $sertification->fill([
            'deadline_bayar' => $validatedData['deadline_bayar'],
            'biaya' => $validatedData['biaya'],
        ]);
        $instruction->fill([
            'content' => $validatedData['content'],
            'user_id' => $request->user()->id,
        ]);
        
        $instruction->save();
        $sertification->save();
        if ($request->boolean('send_notification')) {
            $asesis = Asesi::with(['student.user'])
                ->where('sertification_id', $sertification->id)
                ->where('status', 'dilanjutkan_asesmen')
                ->get();
    
            if ($asesis->isNotEmpty()) {
                $title = 'Update Rincian Pembayaran';
                $body = 'Rincian pembayaran diupdate untuk sertifikasi ' . $sertification->skema->nama_skema;
                foreach ($asesis as $asesi) {
                    $user = $asesi->student->user ?? null;
                    $url = route('asesi.payment.create', [$sertification, $asesi]);
                    $this->sendPushNotification($messaging, $user, $title, $body, $url, 'RincianPembayaranUpdated');
                }
            }
        }
        return redirect()->back()->with('message', 'Rincian pembayaran berhasil disimpan!');
    }

    public function destroy(Sertification $sertification)
    {
        if ($sertification->paymentInstruction) {
            $sertification->paymentInstruction->delete();
        }

        return redirect()->back()->with('message', 'Instruksi pembayaran berhasil dihapus!');
    }
}
