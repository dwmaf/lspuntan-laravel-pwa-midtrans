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
            'tgl_apply_dibuka'=>'required',
            'tgl_apply_ditutup'=>'required',
            'tgl_bayar_ditutup'=>'required',
            'harga'=>'required'
        ]);
        $asesorSkema = $request->input('asesor_skema');
        $asesorSkema = $asesorSkema[0];
        list($asesor_id, $skema_id) = explode(',', $asesorSkema);
        $validatedData['asesor_id'] = $asesor_id;
        $validatedData['skema_id'] = $skema_id;
        Sertification::create($validatedData);
        return redirect('/sertification')->with('Success','Sertifikasi berhasil diunggah, kini asesi bisa mendaftar ke sertifikasi');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sertification $sertification)
    {
        return view('admin.sertifikasi.rinciansertifikasi',[
            'sertification'=>Sertification::where('id', $sertification->id)->with('skema','asesor')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sertification $sertification)
    {
        return view('admin.sertifikasi.editsertifikasi',[
            'sertification'=>$sertification
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sertification $sertification)
    {
        $validatedData = $request->validate([
            'skema_id' => ['required', 'string','max:255'],
        ]);
        // if($request->file('link_foto')) {
        //     if($request->oldImage){
        //         Storage::delete($request->oldImage);
        //     }
        //     $validatedData['link_foto'] = $request->file('link_foto')->store('berita-images');
        // }
        Sertification::where('id', $sertification->id)
            ->update($validatedData);

            return redirect(route('dashboard'))->with('Success','Data Skema berhasil diupdate');
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
        return redirect(route('dashboard'))->with('success', 'Sertifikasi berhasil dihapus');
    }
}
