<?php

namespace App\Http\Controllers;

use App\Models\Asesi;
use App\Models\Sertification;
use Illuminate\Http\Request;

class AsesiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('asesi.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    
    public function create($id)
    {
        return view('asesi.sertifikasi.apply-page',[
            'sertification'=>Sertification::find($id)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request);
        $validatedData=$request->validate([
            'email'=>'required',
            'name'=>'required',
            'nik'=>'required',
            'tmpt_tgl_lhr'=>'required',
            'kelamin'=>'required',
            'kebangsaan'=>'required',
            'no_tlp_hp'=>'required',
            'kualifikasi_pendidikan'=>'required',
            'tujuan_sert'=>'required',
            'makul_nilai'=>'required',
            'apl_1'=>'required',
            'apl_2'=>'required',
            'foto_ktp'=>'required',
            'foto_ktm'=>'required',
            'scan_khs'=>'required',
            'pas_foto'=>'required',
        ]);
        
        Asesi::create($validatedData);
        return redirect('/dashboard')->with('Success','Berhasil daftar Sertifikasi');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asesi $asesi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asesi $asesi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asesi $asesi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asesi $asesi)
    {
        //
    }
}
