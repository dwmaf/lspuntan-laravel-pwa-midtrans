<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Models\Asesi;
use Illuminate\Support\Facades\DB;
use App\Models\Sertification;
use App\Models\Skema;
use App\Models\Asesor;
use App\Models\User;
use App\Traits\SendsPushNotifications;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;

class KelolaSertifikasiController extends Controller
{
    use SendsPushNotifications;
    public function index(Request $request)
    {

        return Inertia::render('Admin/KelolaSertifikasiAdmin', [
            'sertifications_berlangsung' => Sertification::with('skema', 'asesors.user', 'paymentInstruction')
                ->where('status', 'berlangsung')
                ->orderBy('tgl_apply_dibuka', 'desc')
                ->get(),
            'sertifications_selesai' => Sertification::with('skema', 'asesors.user', 'paymentInstruction')
                ->where('status', 'selesai')
                ->orderBy('tgl_apply_ditutup', 'desc')
                ->get(),
            'asesors' => Asesor::with('skemas', 'user')->withCount('sertifications')->get(),
            'skemas' => Skema::all(),
            // 'filters' => ['filter' => $filter],
        ]);
    }

    public function store(Request $request, Messaging $messaging)
    {
        // dd($request);
        $validatedData = $request->validate([
            'skema_id' => 'required',
            'asesor_ids' => 'required|array',
            'asesor_ids.*' => 'exists:asesors,id',
            'tgl_apply_dibuka' => 'required|date',
            'tgl_apply_ditutup' => 'required|date|after_or_equal:tgl_apply_dibuka',
            'deadline' => 'required|date',
            'biaya' => 'required|numeric|min:0',
            'tuk' => 'required',
        ]);
        DB::transaction(function () use ($validatedData, $request, &$sertification) {
            $sertification = Sertification::create([
                'skema_id' => $validatedData['skema_id'],
                'tgl_apply_dibuka' => $validatedData['tgl_apply_dibuka'],
                'tgl_apply_ditutup' => $validatedData['tgl_apply_ditutup'],
                'tuk' => $validatedData['tuk'],
                'status' => 'berlangsung',
            ]);
            if (!empty($validatedData['asesor_ids'])) {
                $sertification->asesors()->attach($validatedData['asesor_ids']);
            }
            $sertification->paymentInstruction()->create([
                'biaya' => $validatedData['biaya'],
                'deadline' => $validatedData['deadline'],
                'user_id' => $request->user()->id,
                'content' => 'Silakan lakukan pembayaran sesuai nominal yang tertera.',
            ]);
        });
        if ($sertification) {
            $recipients = User::role('asesi')->get();
            if ($recipients->isNotEmpty()) {
                $title = 'Sertifikasi Baru Dibuka!';
                $body = "Sertifikasi baru untuk '{$sertification->skema->nama_skema}' telah dibuka. Cek sekarang!";
                $url = route('asesi.sertifikasi.index');
                $this->sendMulticastNotification($messaging, $recipients, $title, $body, $url, 'SertifikasiBaru');
            }
        }
        return redirect()->back()->with('message', 'Sertifikasi berhasil dimulai!');
    }

    public function show($sert_id)
    {
        $sertification = Sertification::with('skema', 'asesors.user', 'paymentInstruction')->withCount('asesis')->findOrFail($sert_id);
        return Inertia::render('Admin/DetailSertifikasiAdmin', [
            'sertification' => $sertification,
            'asesors' => Asesor::with('skemas', 'user')->get(),
            'skemas' => Skema::all(),
        ]);
    }

    public function update($sert_id, Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'skema_id' => 'required',
            'asesor_ids' => 'required|array',
            'asesor_ids.*' => 'exists:asesors,id',
            'tgl_apply_dibuka' => 'required|date',
            'tgl_apply_ditutup' => 'required|date|after_or_equal:tgl_apply_dibuka',
            'deadline' => 'required|date',
            'biaya' => 'required|numeric|min:0',
            'tuk' => 'required',
            'status' => 'required|in:berlangsung,selesai',
        ]);
        $sertification = Sertification::findOrFail($sert_id);
        DB::transaction(function () use ($validatedData, $sertification, $request) {
            $sertification->update([
                'skema_id' => $validatedData['skema_id'],
                'tgl_apply_dibuka' => $validatedData['tgl_apply_dibuka'],
                'tgl_apply_ditutup' => $validatedData['tgl_apply_ditutup'],
                'tuk' => $validatedData['tuk'],
                'status' => $validatedData['status'],
            ]);
            if (isset($validatedData['asesor_ids'])) {
                $sertification->asesors()->sync($validatedData['asesor_ids']);
            } else {
                $sertification->asesors()->sync([]);
            }
            $sertification->paymentInstruction()->updateOrCreate(
                ['sertification_id' => $sertification->id],
                [
                    'biaya' => $validatedData['biaya'],
                    'deadline' => $validatedData['deadline'],
                    'user_id' => $request->user()->id,
                ]
            );
        });
        $sertification->update($validatedData);

        return redirect()->back()->with('message', 'Data Sertifikasi berhasil diupdate');
    }

    public function destroy($sert_id)
    {
        Sertification::destroy($sert_id);
        return to_route('admin.kelolasertifikasi.index')->with('message', 'Sertifikasi berhasil dihapus');
    }


    public function rincian_laporan($sert_id, Request $request)
    {
        return Inertia::render('Admin/LaporanAdmin', [
            'sertification' => Sertification::with('asesors.user', 'skema', 'asesis.student.user')->findOrFail($sert_id)
        ]);
    }
}
