<?php

namespace App\Http\Controllers;

use App\Models\Sertification;
use App\Http\Requests\StoreSertificationRequest;
use App\Http\Requests\UpdateSertificationRequest;
use Illuminate\Http\Request;

class SertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.sertifikasi.index',[
            'sertifications'=>Sertification::all()
        ]);
    }
    public function index_asesi()
    {
        return view('admin.sertifikasi.index',[
            'sertifications'=>Sertification::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sertifikasi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData=$request->validate([
            'skema_id' => ['required'],
        ]);
        Sertification::create($validatedData);
        return redirect(route('dashboard'))->with('Success','Sertifikasi berhasil diunggah, kini asesi bisa mendaftar ke sertifikasi');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sertification $sertification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sertification $sertification)
    {
        return view('admin.sertifikasi.edit',[
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
