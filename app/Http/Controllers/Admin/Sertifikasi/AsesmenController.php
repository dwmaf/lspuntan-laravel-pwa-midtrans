<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Models\Tugasasesmenattachmentfile;
use App\Models\Sertifikat;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;

class AsesmenController extends Controller
{
    public function rincian_asesmen($id, Request $request)
    {
        // dd($id);
        $sertification = Sertification::with([
            'asesi.transaction',
            'pembuatrinciantugasasesmen.asesor'
        ])->find($id);

        // Filter asesi sesuai kriteria
        $filteredAsesi = $sertification->asesi->filter(function ($asesi) {
            $latestTransaction = $asesi->transaction->sortByDesc('created_at')->first();
            return $asesi->status === 'dilanjutkan_asesmen'
                && $latestTransaction
                && $latestTransaction->status === 'bukti_pembayaran_terverifikasi';
        });

        return view('admin.sertifikasi.asesmen.indexasesmen', [
            'sertification' => $sertification,
            'filteredAsesi' => $filteredAsesi
        ]);
    }

    //buat update tugas asesmen di sisi admin/asesor
    public function update_tugas_asesmen($id, Request $request)
    {
        // dd($request);
        $request->validate([
            'rincian_tugas_asesmen' => 'required|string',
        ]);

        $sertification = Sertification::find($id);
        $sertification->rincian_tugas_asesmen = $request->rincian_tugas_asesmen;
        $sertification->rincian_tugasasesmen_dibuat_oleh = $request->user()->id; // Ambil ID user yang login yg buat perubahan
        $sertification->rincian_tugasasesmen_dibuat_pada = now(); // Ambil waktu saat ini
        $sertification->save();
        // Simpan file baru
        if ($request->hasFile('asesmen_attachment_file')) {
            foreach ($request->file('asesmen_attachment_file') as $file) {
                if ($file->isValid()) {
                    $path = FileHelper::storeFileWithUniqueName($request->file('asesmen_attachment_file'), 'asesmen_attachment_file');
                    Tugasasesmenattachmentfile::create([
                        'sertification_id' => $id,
                        'path_file' => $path['[path'],
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }

    // buat nampilin daftar submitan tugas asesmen yg dikirim asesi
    public function rincian_asesmen_asesi($id, $sert_id, Request $request)
    {
        // dd($id);
        return view('admin.sertifikasi.asesmen.rinciansubmitanasesi', [
            'asesi' => Asesi::with('asesiasesmenfiles')->find($id),
            'sertification' => Sertification::find($sert_id)
        ]);
    }    
}
