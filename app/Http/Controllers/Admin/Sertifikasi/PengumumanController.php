<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Models\Pengumumanasesmen;
use App\Notifications\PengumumanBaru;
use App\Models\Pengumumanasesmenfile;
use App\Helpers\FileHelper;
use App\Notifications\PengumumanUpdated;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PengumumanController extends Controller
{

    //buat nampilin halaman daftar pengumuman asesmen di sisi admin/asesor sekaligus untuk buat nambah pengumuman
    public function index_pengumuman_asesmen($sert_id, Request $request)
    {
        // dd($id);
        $sertification = Sertification::with('pengumumanasesmen.pembuatpengumuman.asesor','pengumumanasesmen.pengumumanasesmenfile')->findOrFail($sert_id);
        return Inertia::render('Admin/PengumumanAdmin', [
            'pengumumans' => $sertification->pengumumanasesmen,
            'sertification' => $sertification,
        ]);
    }
    //buat update rincian pra asesmen di sisi admin, NOT USED ANYMORE
    public function store_pengumuman_asesmen($sert_id, Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'rincian_pengumuman_asesmen' => 'required|string',
        ]);
        $validatedData['pengumuman_madeby']= $request->user()->id; // Ambil ID user yang login yg buat perubahan
        $validatedData['sertification_id'] = $sert_id;
        $pengumumanAsesmen = Pengumumanasesmen::create(
            $validatedData
        );
        // Simpan file baru
        if ($request->hasFile('pengumuman_asesmen_attachment_file')) {
            foreach ($request->file('pengumuman_asesmen_attachment_file') as $file) {
                if ($file->isValid()) {
                    $path = FileHelper::storeFileWithUniqueName($file, 'pengumuman_asesmen_attachment_file');
                    Pengumumanasesmenfile::create([
                        'pengumumanasesmen_id' => $pengumumanAsesmen->id,
                        'path_file' => $path['path'],
                    ]);
                }
            }
        }
        // ambil semua Asesi yang cocok
        $asesis = Asesi::with(['student.user']) // pastikan relasi student->user ada
            ->where('sertification_id', $sert_id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        // kirim notifikasi secara individual agar link bisa berisi asesi_id
        foreach ($asesis as $asesi) {
            $user = $asesi->student->user ?? null;
            if ($user) {
                $user->notify(new PengumumanBaru($sert_id, $asesi->id));
            }
        }

        return redirect()->back()->with('message', 'Berhasil membuat pengumuman');
    }
    //buat update pengumuman asesmen di sisi admin/asesor
    public function edit_pengumuman_asesmen($id, $peng_id, Request $request)
    {
        // dd($request);
        $sertification = Sertification::find($id);
        $pengumumanAsesmen = Pengumumanasesmen::find($peng_id);
        return view('admin.sertifikasi.pengumuman.editpengumuman', [
            'pengumumanAsesmen' => $pengumumanAsesmen,
            'sertification' => $sertification,
        ]);
    }
    //buat update pengumuman asesmen di sisi admin/asesor
    public function update_pengumuman_asesmen($id, $peng_id, Request $request)
    {
        // dd($request);
        $request->validate([
            'rincian_pengumuman_asesmen' => 'required|string',
        ]);

        $pengumumanAsesmen = Pengumumanasesmen::find($peng_id);
        $pengumumanAsesmen->rincian_pengumuman_asesmen = $request->rincian_pengumuman_asesmen;
        $pengumumanAsesmen->save();
        // Simpan file baru
        if ($request->hasFile('pengumuman_asesmen_attachment_file')) {
            foreach ($request->file('pengumuman_asesmen_attachment_file') as $file) {
                if ($file->isValid()) {
                    $path = FileHelper::storeFileWithUniqueName($file, 'pengumuman_asesmen_attachment_file');
                    Pengumumanasesmenfile::create([
                        'pengumumanasesmen_id' => $pengumumanAsesmen->id,
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
                $user->notify(new PengumumanUpdated($id, $asesi->id));
            }
        }

        return redirect(route('admin.sertifikasi.assessment-announcement.index',$id))->with('success', 'Pengumuman berhasil diupdate berhasil disimpan!');
    }
    // fungsi ajax buat hapus file dari pengumuman asesmen
    public function ajaxDeletePengumumanAsesmenFile(Request $request)
    {
        $fileId = $request->getContent(); // body request berisi ID file (plain text)
        if (empty($fileId)) {
            return response()->json(['error' => 'File ID tidak valid.'], 400);
        }

        $file = Pengumumanasesmenfile::find($fileId);
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
    public function destroy_pengumuman_asesmen($id, $peng_id, Request $request)
    {
        $pengumuman = Pengumumanasesmen::with('pengumumanasesmenfile')->findOrFail($peng_id);
        $pengumuman->delete();
        return redirect()->back()->with('message', 'Pengumuman berhasil dihapus');
    }

}
