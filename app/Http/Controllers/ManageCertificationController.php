<?php

namespace App\Http\Controllers;

use App\Models\Asesi;
use Illuminate\Http\Request;
use App\Models\Sertification;

class ManageCertificationController extends Controller
{
    // public function getAsesor($skemaId)
    // {
    //     $asesors = Skema::find($skemaId)->asesors;
    //     return response()->json(['asesor' => $asesors]);
    // }
    // AsesiController.php
    public function apply_sertifikasi($id, Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        // dd($student);
        return view('asesi.sertifikasi.apply-page', [
            'sertification' => Sertification::with('skema')->find($id),
            'student' => $student,
            'user' => $user
        ]);
    }
    public function list_asesi($id, Request $request)
    {
        // dd($student);
        return view('admin.sertifikasi.pendaftar.index', [
            'asesis' => Asesi::all(),
        ]);
    }
    public function rincian_data_asesi($id, Request $request)
    {
        return view('admin.sertifikasi.pendaftar.rincian', [
            'asesi' => Asesi::with('student.user')->find($id)
        ]);
    }
    public function updateStatus($id, $sertification_id, Request $request)
    {
        $asesi = Asesi::find($id);

        // Memperbarui status sesuai dengan yang diterima dari form
        $asesi->status = $request->status;
        $asesi->save();
        // dd(route('rincian_data_asesi', ['id' => $id]));
        return redirect()->route('list_asesi', ['id' => $sertification_id])->with('success', 'Status berhasil diperbarui');
    }
    public function rincian_praasesmen($id, Request $request)
    {
        // dd($id);
        return view('admin.sertifikasi.praasesmen.index', [
            'sertification' => Sertification::find($id)
        ]);
    }

    public function update_rincian_praasesmen($id, Request $request)
    {
        // dd($request);
        $validated = $request->validate([
            'rincian_praasesmen' => 'required|string',
        ]);

        // Misalnya simpan ke database
        $sertification = Sertification::find($id);
        $sertification->rincian_praasesmen = $request->rincian_praasesmen;
        $sertification->save();

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }

    public function rincian_praasesmen_asesi($id, Request $request)
    {
        // dd($id);
        return view('asesi.sertifikasi.praasesmen.index', [
            'sertification' => Sertification::find($id)
        ]);   
    }
    public function rincian_bayar_asesi(Request $request)
    {
        // dd($request);
        return view('asesi.sertifikasi.bayar.index', [
            'asesi_id'=>$request->asesi_id,
            'biaya'=>$request->biaya,
            'name'=>$request->name,
            'email'=>$request->email,
            'no_tlp_hp'=>$request->no_tlp_hp,
        ]);
    }
}
