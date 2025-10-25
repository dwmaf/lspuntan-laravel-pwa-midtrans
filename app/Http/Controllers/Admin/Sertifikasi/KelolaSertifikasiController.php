<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Models\Sertification;
use App\Models\Skema;
use App\Models\Asesor;
use Illuminate\Http\Request;
use Inertia\Inertia;


class KelolaSertifikasiController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'semua');
        $sertifikasiSelesaiQuery = Sertification::with('skema', 'asesor')
            ->where('status', 'selesai');

        if ($filter === 'bulan_ini') {
            $sertifikasiSelesaiQuery->whereMonth('tgl_apply_ditutup', now()->month)
                                    ->whereYear('tgl_apply_ditutup', now()->year);
        } elseif ($filter === '3_bulan') {
            $sertifikasiSelesaiQuery->where('tgl_apply_ditutup', '>=', now()->subMonths(3));
        } elseif ($filter === 'tahun_ini') {
            $sertifikasiSelesaiQuery->whereYear('tgl_apply_ditutup', now()->year);
        }
        return Inertia::render('Admin/KelolaSertifikasiAdmin',[
            'sertifications_berlangsung' => Sertification::with('skema','asesor')
                                        ->where('status', 'berlangsung')
                                        ->orderBy('tgl_apply_dibuka', 'desc')
                                        ->get(),
            'sertifications_selesai' => $sertifikasiSelesaiQuery
                                        ->orderBy('tgl_apply_ditutup', 'desc')
                                        ->get(),
            'asesors'=>Asesor::with('skemas','user')->get(),
            'skemas'=>Skema::all(),
            'filters' => ['filter' => $filter],
        ]);
    }
    
    public function store(Request $request)
    {
        // dd($request);
        $validatedData=$request->validate([
            'asesor_skema'=>'required',
            'tgl_apply_dibuka'=>'required|date',
            'tgl_apply_ditutup'=>'required|date|after_or_equal:tgl_apply_dibuka',
            'tgl_bayar_ditutup'=>'required|date',
            'harga'=>'required|numeric|min:0',
            'tuk'=>'required',
        ]);
        list($asesor_id, $skema_id) = explode(',', $request->input('asesor_skema'));
        $validatedData['asesor_id'] = $asesor_id;
        $validatedData['skema_id'] = $skema_id;
        $validatedData['status'] = 'berlangsung';
        unset($validatedData['asesor_skema']);
        Sertification::create($validatedData);
        return redirect()->back()->with('message','Sertifikasi berhasil dimulai!');
    }

    public function show($sert_id)
    {
        $sertification = Sertification::with('skema','asesor.user')->findOrFail($sert_id);
        return Inertia::render('Admin/DetailSertifikasiAdmin',[
            'sertification'=>$sertification,
            'asesors'=>Asesor::with('skemas','user')->get(),
            'skemas'=>Skema::all(),
        ]);
    }

    public function update($sert_id, Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'asesor_skema' => 'required',
            'tgl_apply_dibuka' => 'required|date',
            'tgl_apply_ditutup' => 'required|date|after_or_equal:tgl_apply_dibuka',
            'tgl_bayar_ditutup' => 'required|date',
            'harga' => 'required|numeric|min:0',
            'tuk'=>'required',
            'status'=>'required',
        ]);

        list($asesor_id, $skema_id) = explode(',', $request->input('asesor_skema'));
        $validatedData['asesor_id'] = $asesor_id;
        $validatedData['skema_id'] = $skema_id;
        unset($validatedData['asesor_skema']);
        $sertification = Sertification::findOrFail($sert_id);
        $sertification->update($validatedData);

        return redirect(route('admin.kelolasertifikasi.show',$sertification->id))->with('message', 'Data Sertifikasi berhasil diupdate');
    }

    public function destroy($sert_id)
    {
        Sertification::destroy($sert_id);
        return to_route('admin.kelolasertifikasi.index')->with('message', 'Sertifikasi berhasil dihapus');
    }

    
    public function rincian_laporan($sert_id, Request $request)
    {
        return Inertia::render('Admin/LaporanAdmin', [
            'sertification' => Sertification::with('asesor.user', 'skema', 'asesi')->findOrFail($sert_id)
        ]);
    }

}