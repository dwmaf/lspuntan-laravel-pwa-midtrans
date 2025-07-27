<?php

namespace App\Http\Controllers;

use App\Models\Sertification;
use App\Models\Skema;
use App\Models\Asesor;
use Illuminate\Http\Request;

class SertificationController extends Controller
{
    // Nampilin daftar sertifikasi yg sedang berlangsung maupun yg sudah selesai, serta halaman untuk memulai sertifikasi
    public function index()
    {
        return view('admin.sertifikasi.mulaisertifikasi',[
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
    

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     return view('admin.sertifikasi.create');
    // }

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
            'status'=>'required'
        ]);
        list($asesor_id, $skema_id) = explode(',', $request->input('asesor_skema'));
        $validatedData['asesor_id'] = $asesor_id;
        $validatedData['skema_id'] = $skema_id;
        unset($validatedData['asesor_skema']);
        Sertification::create($validatedData);
        return redirect()->back()->with('success','Sertifikasi berhasil diunggah, kini asesi bisa mendaftar ke sertifikasi');
    }

    // untuk  nampilin data sertifikasi yg dimulai
    public function show(Sertification $sertification)
    {
        $sertification->load('skema','asesor');
        return view('admin.sertifikasi.rinciansertifikasi',[
            'sertification'=>$sertification,
        ]);
    }

    // untuk nampilin halaman edit sertifikasi yg udh dimulai sebelumnya
    public function edit(Sertification $sertification)
    {
        $sertification->load('skema','asesor');
        return view('admin.sertifikasi.editsertifikasi',[
            'sertification'=>$sertification,
            'asesors'=>Asesor::with('skemas','user')->get(),
        ]);
    }

    // untuk mengupdate sertifikasi yg tadi diedit
    public function update(Request $request, Sertification $sertification)
    {
        $validatedData = $request->validate([
            'asesor_skema' => 'required',
            'tgl_apply_dibuka' => 'required|date',
            'tgl_apply_ditutup' => 'required|date|after_or_equal:tgl_apply_dibuka',
            'tgl_bayar_ditutup' => 'required|date|after_or_equal:tgl_apply_ditutup',
            'harga' => 'required|numeric|min:0'
        ]);

        list($asesor_id, $skema_id) = explode(',', $request->input('asesor_skema'));
        $validatedData['asesor_id'] = $asesor_id;
        $validatedData['skema_id'] = $skema_id;
        unset($validatedData['asesor_skema']);

        $sertification->update($validatedData);

        return redirect('/sertification')->with('success', 'Data Sertifikasi berhasil diupdate');
    }

    // untuk menghapus data sertifikasi yg udh dimulai tadi
    public function destroy(Sertification $sertification)
    {
        // if($skema->link_foto){
        //     Storage::delete($skema->link_foto);
        // }
        Sertification::destroy($sertification->id);
        return redirect()->back()->with('success', 'Sertifikasi berhasil dihapus');
    }

    // untuk mengubah status sertifikasi dari sedang berlangsung ke dimulai
    public function complete(Request $request)
    {
        $sertification = Sertification::find($request->id);
        $sertification->update(['status' => 'selesai']);
        return redirect()->route('admin.sertification.show', $sertification->id)->with('success', 'Status sertifikasi berhasil diubah menjadi Selesai.');
    }
}
