<?php

namespace App\Http\Controllers;

use App\Models\Asesi;
use App\Models\Sertification;
use Illuminate\Support\Facades\Storage;
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
        // dd($request);
        $user = $request->user();
        $student = $user->student;
        $request->validate([
            'sertification_id'=>'required|string|max:255',
            'student_id'=>'required|string|max:255',
            'name'=>'required|string|max:255',
            'nik'=>'required|string|max:255',
            'tmpt_tgl_lhr'=>'required|string|max:255',
            'kelamin'=>'required|string|max:255',
            'kebangsaan'=>'required|string|max:255',
            'no_tlp_hp'=>'required|string|max:255',
            'kualifikasi_pendidikan'=>'required|string|max:255',
            'tujuan_sert'=>'required|string|max:255',
            'makul_nilai'=>'required|string|max:255',
            'apl_1'=>'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'apl_2'=>'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktm' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_khs' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pas_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($user->name !== $request->name) {
            $user->name = $request->name;
            $user->save();
        }
        $student->fill($request->only([
            'nik',
            'tmpt_tgl_lhr',
            'kelamin',
            'kebangsaan',
            'no_tlp_rmh',
            'no_tlp_kntr',
            'no_tlp_hp',
            'kualifikasi_pendidikan',
        ]));

        foreach (['foto_ktp', 'foto_ktm', 'foto_khs', 'pas_foto'] as $fileField) {
            if ($request->hasFile($fileField)) {
                // Cek jika file sebelumnya ada (tidak null atau kosong)
                if ($student->$fileField && Storage::disk('public')->exists($student->$fileField)) {
                    // Hapus file lama jika ada
                    Storage::disk('public')->delete($student->$fileField);
                }
                // Simpan file baru
                $student->$fileField = $request->file($fileField)->store($fileField, 'public');
            }
        }
        if ($student->isDirty()) {
            $student->save();
        }
        $apl_1_path = $request->file('apl_1')->store('apl_files', 'public');
        $apl_2_path = $request->file('apl_2')->store('apl_files', 'public');

        Asesi::create([
            'student_id' => $request->input('student_id'),
            'status'=> $request->input('status'),
            'sertification_id' => $request->input('sertification_id'),
            'tujuan_sert' => $request->input('tujuan_sert'),
            'makul_nilai' => $request->input('makul_nilai'),
            'apl_1' => $apl_1_path,
            'apl_2' => $apl_2_path,
        ]);
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
