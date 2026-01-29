<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Models\Asesi;
use App\Exports\LaporanSertifikasiExport;
use Illuminate\Support\Facades\DB;
use App\Models\Sertification;
use App\Models\Skema;
use App\Models\Asesor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;
use App\Traits\SendsPushNotifications;
use Maatwebsite\Excel\Facades\Excel;

class KelolaSertifikasiController extends Controller
{
    use SendsPushNotifications;
    public function index(Request $request)
    {

        return Inertia::render('Admin/KelolaSertifikasiAdmin', [
            'sertifications_berlangsung' => Sertification::with('skema', 'asesors.user')
                ->withCount('asesis')
                ->where('status', 'berlangsung')
                ->orderBy('tgl_apply_dibuka', 'desc')
                ->get(),
            'sertifications_selesai' => Sertification::with('skema', 'asesors.user')
                ->withCount('asesis')
                ->when($request->input('date_from'), function ($query, $dateFrom) {
                    $query->whereDate('tgl_apply_dibuka', '>=', $dateFrom);
                })
                ->when($request->input('date_to'), function ($query, $dateTo) {
                    $query->whereDate('tgl_apply_ditutup', '<=', $dateTo);
                })
                ->when($request->input('asesor'), function ($query, $asesorId) {
                    $query->whereHas('asesors', function ($subQuery) use ($asesorId) {
                        $subQuery->where('asesors.id', $asesorId);
                    });
                })
                ->when($request->input('skema'), function ($query, $skema) {
                    $query->whereHas('skema', fn($q) => $q->where('id', $skema));
                })
                ->whereIn('status', ['selesai', 'dibatalkan'])
                ->orderBy('tgl_apply_ditutup', 'desc')
                ->latest()
                ->paginate(10)
                ->onEachSide(0)
                ->withQueryString(),
            'asesors' => Asesor::with('skemas', 'user')->withCount('sertifications')->get(),
            'skemas' => Skema::all(),
            'activeSkemas' => Skema::where('is_active', true)->get(),
            'filters' => $request->only(['date_from', 'date_to', 'asesor', 'skema', 'tab']),

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
            'tgl_asesmen_mulai' => 'nullable|date',
            'tgl_asesmen_selesai' => 'nullable|date|after_or_equal:tgl_asesmen_mulai',
            'tuk' => 'nullable|string|max:255',
            'biaya' => 'required|numeric|min:0',
            'no_rek' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'atas_nama_rek' => 'required|string|max:255',
        ]);
        
        $sertification = null;

        DB::transaction(function () use ($validatedData, $request, &$sertification) {
            $sertification = Sertification::create([
                'skema_id' => $validatedData['skema_id'],
                'tgl_apply_dibuka' => $validatedData['tgl_apply_dibuka'],
                'tgl_apply_ditutup' => $validatedData['tgl_apply_ditutup'],
                'tgl_asesmen_mulai' => $validatedData['tgl_asesmen_mulai'] ?? null,
                'tgl_asesmen_selesai' => $validatedData['tgl_asesmen_selesai'] ?? null,
                'biaya' => $validatedData['biaya'],
                'no_rek' => $validatedData['no_rek'],
                'bank' => $validatedData['bank'],
                'atas_nama_rek' => $validatedData['atas_nama_rek'],
                'tuk' => $validatedData['tuk'] ?? null,
                'status' => 'berlangsung',
            ]);
            
            if (!empty($validatedData['asesor_ids'])) {
                $sertification->asesors()->attach($validatedData['asesor_ids']);
            }
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
        return redirect(route('admin.kelolasertifikasi.show', $sertification))->with('message', 'Sertifikasi berhasil dimulai!');
    }

    public function show(Sertification $sertification)
    {
        $sertification->load('skema', 'asesors.user')->loadCount('asesis');
        return Inertia::render('Admin/DetailSertifikasiAdmin', [
            'sertification' => $sertification,
            'asesors' => Asesor::with('skemas', 'user')->get(),
            'skemas' => Skema::all(),
            'activeSkemas' => Skema::where('is_active', true)->get(),
        ]);
    }

    public function update(Sertification $sertification, Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'skema_id' => 'required',
            'asesor_ids' => 'required|array',
            'asesor_ids.*' => 'exists:asesors,id',
            'tgl_apply_dibuka' => 'required|date',
            'tgl_apply_ditutup' => 'required|date|after_or_equal:tgl_apply_dibuka',
            'tgl_asesmen_mulai' => 'nullable|date',
            'tgl_asesmen_selesai' => 'nullable|date|after_or_equal:tgl_asesmen_mulai',
            'biaya' => 'required|numeric|min:0',
            'tuk' => 'nullable|string|max:255',
            'no_rek' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'atas_nama_rek' => 'required|string|max:255',
            'status' => 'required|in:berlangsung,selesai,dibatalkan',
        ]);
        
        DB::transaction(function () use ($validatedData, $sertification, $request) {
            $sertification->update([
                'skema_id' => $validatedData['skema_id'],
                'tgl_apply_dibuka' => $validatedData['tgl_apply_dibuka'],
                'tgl_apply_ditutup' => $validatedData['tgl_apply_ditutup'],
                'tgl_asesmen_mulai' => $validatedData['tgl_asesmen_mulai'] ?? null,
                'tgl_asesmen_selesai' => $validatedData['tgl_asesmen_selesai'] ?? null,
                'biaya' => $validatedData['biaya'],
                'no_rek' => $validatedData['no_rek'],
                'bank' => $validatedData['bank'],
                'atas_nama_rek' => $validatedData['atas_nama_rek'],
                'tuk' => $validatedData['tuk'] ?? null,
                'status' => $validatedData['status'],
            ]);
            if (isset($validatedData['asesor_ids'])) {
                $sertification->asesors()->sync($validatedData['asesor_ids']);
            } else {
                $sertification->asesors()->sync([]);
            }
        });
        // $sertification->update($validatedData);

        return redirect()->back()->with('message', 'Data Sertifikasi berhasil diupdate');
    }

    public function cancel(Sertification $sertification)
    {
        if ($sertification->status === 'selesai') {
            return back()->with('error', 'Gagal: Sertifikasi yang sudah selesai tidak dapat dibatalkan.');
        }

        $sertification->update(['status' => 'dibatalkan']);
        return back()->with('message', 'Sertifikasi berhasil dibatalkan.');
    }
    
    public function export_excel(Sertification $sertification)
    {
        $sertification->load('skema', 'asesis.student.user', 'asesors.user');
        $fileName = 'Laporan_Sertifikasi_' . Str::slug($sertification->skema->nama_skema) . '.xlsx';
        return Excel::download(new LaporanSertifikasiExport($sertification), $fileName);
    }
}
