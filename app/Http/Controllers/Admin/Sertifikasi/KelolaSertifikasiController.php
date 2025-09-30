<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Models\Sertification;
use App\Models\Skema;
use App\Models\Asesor;
use Illuminate\Http\Request;

class KelolaSertifikasiController extends Controller
{
    // Nampilin daftar sertifikasi yg sedang berlangsung maupun yg sudah selesai, serta halaman untuk memulai sertifikasi
    public function index()
    {
        return view('admin.sertifikasi.kelolasertifikasi.mulaisertifikasi',[
            'sertifications_berlangsung' => Sertification::with('skema','asesor')
                                        ->where('status', 'berlangsung')
                                        ->orderBy('tgl_apply_dibuka', 'desc')
                                        ->get(),
            'sertifications_selesai' => Sertification::with('skema','asesor')
                                        ->where('status', 'selesai')
                                        ->orderBy('tgl_apply_ditutup', 'desc')
                                        ->get(),
            'asesors'=>Asesor::with('skemas','user')->get(),
            'skemas'=>Skema::all()
        ]);
    }
    

    // untuk menyimpan data sertifikasi yg dimulai
    public function store(Request $request)
    {
        // dd($request);
        $validatedData=$request->validate([
            'asesor_skema'=>'required',
            'tgl_apply_dibuka'=>'required|date',
            'tgl_apply_ditutup'=>'required|date|after_or_equal:tgl_apply_dibuka',
            'tgl_bayar_ditutup'=>'required|date',
            'harga'=>'required|numeric|min:0',
            'status'=>'required',
            'tuk'=>'required',
        ]);
        list($asesor_id, $skema_id) = explode(',', $request->input('asesor_skema'));
        $validatedData['asesor_id'] = $asesor_id;
        $validatedData['skema_id'] = $skema_id;
        unset($validatedData['asesor_skema']);
        Sertification::create($validatedData);
        return redirect()->back()->with('success','Sertifikasi berhasil diunggah, kini asesi bisa mendaftar ke sertifikasi');
    }

    // untuk  nampilin data sertifikasi yg dimulai
    public function show($sert_id)
    {
        $sertification = Sertification::with('skema','asesor')->find($sert_id);
        return view('admin.sertifikasi.kelolasertifikasi.rinciansertifikasi',[
            'sertification'=>$sertification,
        ]);
    }

    // untuk nampilin halaman edit sertifikasi yg udh dimulai sebelumnya
    public function edit($sert_id)
    {
        $sertification = Sertification::with('skema','asesor')->find($sert_id);
        return view('admin.sertifikasi.kelolasertifikasi.editsertifikasi',[
            'sertification'=>$sertification,
            'asesors'=>Asesor::with('skemas','user')->get(),
        ]);
    }

    // untuk mengupdate sertifikasi yg tadi diedit
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
        $sertification = Sertification::find($sert_id);
        $sertification->update($validatedData);

        return redirect(route('admin.kelolasertifikasi.show',$sertification->id))->with('success', 'Data Sertifikasi berhasil diupdate');
    }

    // untuk menghapus data sertifikasi yg udh dimulai tadi
    public function destroy($sert_id)
    {
        Sertification::destroy($sert_id);
        return redirect()->back()->with('success', 'Sertifikasi berhasil dihapus');
    }

    
    public function rincian_laporan($sert_id, Request $request)
    {
        // dd($request);

        return view('admin.sertifikasi.kelolasertifikasi.laporansertifikasi', [
            'sertification' => Sertification::with('asesor', 'skema', 'asesi')->find($sert_id)
        ]);
    }

    //fungsi ajax buat memfilter riwayat sertifikasi
    public function filter_riwayat_sertifikasi(Request $request)
    {
        $filter = $request->input('filter');

        $query = Sertification::with('skema')->where('status', 'selesai');

        switch ($filter) {
            case 'bulan_ini':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case '3_bulan':
                $query->where('created_at', '>=', now()->subMonths(3));
                break;
            case 'tahun_ini':
                $query->whereYear('created_at', now()->year);
                break;
            default:
                break;
        }

        $sertifications = $query->get();

        return response()->json([
            'sertifications' => $sertifications
        ]);
    }
}