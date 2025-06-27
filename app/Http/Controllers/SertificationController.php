<?php

namespace App\Http\Controllers;

use App\Models\Sertification;
use App\Models\Skema;
use App\Models\Asesor;
use Illuminate\Http\Request;

class SertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.sertifikasi.mulaisertifikasi',[
            'sertifications'=>Sertification::with('skema','asesor')->get(),
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $validatedData=$request->validate([
            'asesor_skema'=>'required',
            'tgl_apply_dibuka'=>'required|date',
            'tgl_apply_ditutup'=>'required|date|after_or_equal:tgl_apply_dibuka',
            'tgl_bayar_ditutup'=>'required|date|after_or_equal:tgl_apply_ditutup',
            'harga'=>'required|numeric|min:0'
        ]);
        list($asesor_id, $skema_id) = explode(',', $request->input('asesor_skema'));
        $validatedData['asesor_id'] = $asesor_id;
        $validatedData['skema_id'] = $skema_id;
        unset($validatedData['asesor_skema']);
        Sertification::create($validatedData);
        return redirect()->back()->with('success','Sertifikasi berhasil diunggah, kini asesi bisa mendaftar ke sertifikasi');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sertification $sertification)
    {
        $sertification->load('skema','asesor');
        return view('admin.sertifikasi.rinciansertifikasi',[
            'sertification'=>$sertification,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sertification $sertification)
    {
        $sertification->load('skema','asesor');
        return view('admin.sertifikasi.editsertifikasi',[
            'sertification'=>$sertification,
            'asesors'=>Asesor::with('skemas','user')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sertification $sertification)
    {
        // if($skema->link_foto){
        //     Storage::delete($skema->link_foto);
        // }
        Sertification::destroy($sertification->id);
        return redirect()->back()->with('success', 'Sertifikasi berhasil dihapus');
    }
}
