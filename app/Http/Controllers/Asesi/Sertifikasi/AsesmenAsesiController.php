<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\Tugasasesmenattachmentfile;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use App\Models\Asesiasesmenfile;
use App\Notifications\AsesiUploadBuktiPembayaran;
use App\Notifications\AsesiUploadTugasAsesmen;
use Inertia\Inertia;

class AsesmenAsesiController extends Controller
{

    public function index_asesmen_asesi($sert_id, $asesi_id, Request $request)
    {
        // dd($id);
        NotificationController::markAsRead(($request));
        return Inertia::render('asesi.sertifikasi.asesmen.asesi-index-asesmen', [
            'sertification' => Sertification::with('pembuatrinciantugasasesmen.asesor')->findOrFail($sert_id),
            'asesi' => Asesi::with('asesiasesmenfiles')->findOrFail($asesi_id)
        ]);
    }

    public function update_asesmen_asesi($sert_id, $asesi_id, Request $request)
    {
        // dd($request);
        $request->validate([
            'asesiasesmenfiles' => 'required|array|max:5',
            'asesiasesmenfiles.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB
        ]);
        if ($request->hasFile('asesiasesmenfiles')) {
            // 1. Hapus file lama dari DB dan storage untuk tipe ini
            $oldFiles = Asesiasesmenfile::where('asesi_id', $asesi_id)
                ->get();
            foreach ($oldFiles as $oldFile) {
                if ($oldFile && Storage::disk('public')->exists($oldFile->path_file)) {
                    Storage::disk('public')->delete($oldFile->path_file);
                }
                $oldFile->delete();
            }
            foreach ($request->file('asesiasesmenfiles') as $file) {
                $fileData = FileHelper::storeFileWithUniqueName($file, "asesi_asesmen_attachments");
                Asesiasesmenfile::create([
                    'asesi_id' => $asesi_id,
                    'path_file' => $fileData['path'],
                ]);
            }
        }
        // ambil semua Asesi yang cocok
        $sertification = Sertification::with(['asesor.user']) // pastikan relasi student->user ada
            ->find($sert_id);
        $asesor = $sertification->asesor->user;
        $asesor->notify(new AsesiUploadTugasAsesmen($sert_id, $asesi_id));
        
        return redirect()->back()->with('message', 'Berhasil unggah file asesmen.');
    }

    // fungsi ajax buat hapus file dari tugas asesmen
    public function destroyTugasAsesmenFile($file_id)
    {
        $file = Asesiasesmenfile::find($file_id);
        if ($file) {
            // Hapus file fisik
            if (Storage::disk('public')->exists($file->path_file)) {
                Storage::disk('public')->delete($file->path_file);
            }
            // Hapus record database
            $file->delete();
            return redirect()->back()->with('message', 'File berhasil dihapus.');
        }
    }
}
