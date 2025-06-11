<?php

namespace App\Http\Controllers;

use App\Models\Asesi;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ManageCertificationController extends Controller
{
    // public function getAsesor($skemaId)
    // {
    //     $asesors = Skema::find($skemaId)->asesors;
    //     return response()->json(['asesor' => $asesors]);
    // }
    // AsesiController.php

    // buat nampilin daftar sertifikasi yg tersedia di sisi asesi
    public function asesi_index_sertifikasi(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        
        return view('asesi.sertifikasi.asesi-index-sertifikasi', [
            'sertifications' => Sertification::with('skema')->get(),
            'asesi' => Asesi::where('student_id', $student->id)->get()->keyBy('sertification_id')
        ]);
    }
    public function asesi_apply_sertif_dropzone_upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // 5MB, sesuaikan dengan maxFilesize Dropzone
            'file_type' => 'required|string|in:surat_ket_magang,sertif_pelatihan,dok_pendukung_lain',
        ]);

        $file = $request->file('file');
        $fileType = $request->input('file_type');
        $originalFilename = $file->getClientOriginalName();
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Simpan ke direktori sementara
        $tempPath = $file->storeAs("dropzone_temp/{$fileType}", $filename, 'public');

        if ($tempPath) {
            return response()->json([
                'temp_path' => $tempPath,
                'original_filename' => $originalFilename
            ]);
        }
        return response()->json(['error' => 'Could not save file.'], 500);
    }

    public function asesi_apply_sertif_dropzone_remove(Request $request)
    {
        $request->validate(['temp_path' => 'required|string']);
        $tempPath = $request->input('temp_path');

        if ($tempPath && Storage::disk('public')->exists($tempPath)) {
            // Pastikan path tidak keluar dari direktori yang diharapkan untuk keamanan
            if (Str::startsWith($tempPath, 'dropzone_temp/')) {
                Storage::disk('public')->delete($tempPath);
                return response()->json(['success' => 'File deleted.']);
            }
            return response()->json(['error' => 'Invalid path.'], 400);
        }
        return response()->json(['error' => 'File not found.'], 404);
    }
    // buat nampilin halaman apply page sertifikasi di sisi asesi
    public function apply_sertifikasi($id, Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        // dd($student);
        // Ambil semua asesi milik student
        // $asesiBySertificationId = $student->asesi->keyBy('sertification_id');
        return view('asesi.sertifikasi.apply-page', [
            'sertification' => Sertification::with('skema')->find($id),
            'student' => $student,
            'user' => $user,
        ]);
    }
    public function show_applied_sertifikasi($sert_id, $asesi_id, Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $asesi = Asesi::with('asesiattachmentfiles')->find($asesi_id);
        // dd($student);
        return view('asesi.sertifikasi.show-applied-page', [
            'sertification' => Sertification::with('skema')->find($sert_id),
            'student' => $student,
            'asesi' => $asesi
        ]);
    }
    // buat nampilin halaman edit apply page sertifikasi di sisi asesi
    public function edit_apply_sertifikasi($sert_id, $asesi_id, Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        // dd($student);
        return view('asesi.sertifikasi.apply-page', [
            'sertification' => Sertification::with('skema')->find($sert_id),
            'student' => $student,
            'asesi' => Asesi::with('asesiasesmenfile')->find($asesi_id)
            
        ]);
    }

    //buat nampilin daftar asesi di sisi admin
    public function list_asesi($id, Request $request)
    {
        // dd($student);
        return view('admin.sertifikasi.pendaftar.index', [
            'asesis' => Asesi::all(),
        ]);
    }

    //buat nampilin rincian data asesi di sisi admin
    public function rincian_data_asesi($id, Request $request)
    {
        return view('admin.sertifikasi.pendaftar.rincian', [
            'asesi' => Asesi::with('student.user')->find($id)
        ]);
    }

    //buat memperbaharui status asesi di sisi admin
    public function updateStatus($id, $sertification_id, Request $request)
    {
        $asesi = Asesi::find($id);

        // Memperbarui status sesuai dengan yang diterima dari form
        $asesi->status = $request->status;
        $asesi->save();
        // dd(route('rincian_data_asesi', ['id' => $id]));
        return redirect()->route('list_asesi', ['id' => $sertification_id])->with('success', 'Status berhasil diperbarui');
    }
    //buat nampilin halaman rincian pra asesmen di sisi admin
    public function rincian_praasesmen($id, Request $request)
    {
        // dd($id);
        return view('admin.sertifikasi.praasesmen.indexpraasesmen', [
            'sertification' => Sertification::find($id)
        ]);
    }
    //buat update rincian pra asesmen di sisi admin
    public function update_rincian_praasesmen($id, Request $request)
    {
        // dd($request);
        $validated = $request->validate([
            'rincian_praasesmen' => 'required|string',
        ]);

        $sertification = Sertification::find($id);
        $sertification->rincian_praasesmen = $request->rincian_praasesmen;
        $sertification->save();
        if ($request->hasFile('praasesmen_attachment_file')) {
            $existing = $sertification->praasesmen_attachment_file()->count();
            $newFiles = count($request->file('praasesmen_attachment_file'));

            if ($existing + $newFiles > 5) {
                return redirect()->back()->withErrors(['praasesmen_attachment_file' => 'Total lampiran maksimal 5 file.']);
            }
            foreach ($request->file('praasesmen_attachment_file') as $file) {
                $path = $file->store('praasesmen_attachment_file', 'public');

                $sertification->praasesmenfile()->create([
                    'praasesmen_attachment_file' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }
    //buat nampilin halaman rincian asesmen di sisi admin/asesor
    public function rincian_asesmen($id, Request $request)
    {
        // dd($id);
        return view('admin.sertifikasi.asesmen.indexasesmen', [
            'sertification' => Sertification::find($id)
        ]);
    }
    //buat update rincian asesmen di sisi admin/asesor
    public function update_rincian_asesmen($id, Request $request)
    {
        // dd($request);
        $validated = $request->validate([
            'rincian_praasesmen' => 'required|string',
        ]);

        $sertification = Sertification::find($id);
        $sertification->rincian_praasesmen = $request->rincian_praasesmen;
        $sertification->save();
        if ($request->hasFile('asesmen_attachment_file')) {
            $existing = $sertification->asesmen_attachment_file()->count();
            $newFiles = count($request->file('asesmen_attachment_file'));

            if ($existing + $newFiles > 5) {
                return redirect()->back()->withErrors(['asesmen_attachment_file' => 'Total lampiran maksimal 5 file.']);
            }
            foreach ($request->file('asesmen_attachment_file') as $file) {
                $path = $file->store('asesmen_attachment_file', 'public');

                $sertification->praasesmenfile()->create([
                    'asesmen_attachment_file' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }
    //buat nampilin halaman rincian pra asesmen di sisi asesi
    public function rincian_praasesmen_asesi($id, Request $request)
    {
        // dd($id);
        return view('asesi.sertifikasi.praasesmen.asesi-index-praasesmen', [
            'sertification' => Sertification::find($id)
        ]);
    }
    //buat nampilin halaman rincian asesmen di sisi asesi
    public function rincian_asesmen_asesi($id, Request $request)
    {
        // dd($id);
        return view('asesi.sertifikasi.asesmen.asesi-index-asesmen', [
            'sertification' => Sertification::find($id)
        ]);
    }
    //buat nampilin halaman rincian pembayaran di sisi asesi
    public function rincian_bayar_asesi(Request $request)
    {
        // dd($request);
        return view('asesi.sertifikasi.bayar.index', [
            'asesi_id' => $request->asesi_id,
            'biaya' => $request->biaya,
            'name' => $request->name,
            'email' => $request->email,
            'no_tlp_hp' => $request->no_tlp_hp,
        ]);
    }
}
