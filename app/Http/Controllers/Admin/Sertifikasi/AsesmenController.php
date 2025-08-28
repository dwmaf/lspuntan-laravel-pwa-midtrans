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
use App\Notifications\TugasAsesmenBaru;
use Illuminate\Support\Facades\Notification;

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
        // ambil semua Asesi yang cocok
        $asesis = Asesi::with(['student.user']) // pastikan relasi student->user ada
            ->where('sertification_id', $id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        // kirim notifikasi secara individual agar link bisa berisi asesi_id
        foreach ($asesis as $asesi) {
            $user = $asesi->student->user ?? null;
            if ($user) {
                Notification::send($user, new TugasAsesmenBaru($id, $asesi->id));
            }
        }

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }

    // buat nampilin daftar submitan tugas asesmen yg dikirim asesi
    public function rincian_asesmen_asesi($sert_id, $asesi_id, Request $request)
    {
        // dd($id);
        return view('admin.sertifikasi.asesmen.rinciansubmitanasesi', [
            'asesi' => Asesi::with('asesiasesmenfiles')->find($asesi_id),
            'sertification' => Sertification::find($sert_id)
        ]);
    }    

    public function ajaxDeleteAsesmenFile(Request $request)
    {
        $fileId = $request->getContent(); // body request berisi ID file (plain text)
        if (empty($fileId)) {
            return response()->json(['error' => 'File ID tidak valid.'], 400);
        }

        $file = Tugasasesmenattachmentfile::find($fileId);
        if ($file) {
            // Hapus file fisik
            if (Storage::disk('public')->exists($file->path_file)) {
                Storage::disk('public')->delete($file->path_file);
            }
            // Hapus record database
            $file->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }
    }
}
