<?php

namespace App\Http\Controllers;

use App\Models\Asesi;
use App\Models\Pengumumanasesmen;
use App\Models\Pengumumanasesmenfile;
use App\Models\Tugasasesmenattachmentfile;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Helpers\FileHelper;

class ManageCertificationController extends Controller
{

    private function storeFileWithUniqueName(UploadedFile $file, string $baseDirectory): array
    {
        // id unik berdasarkan timestamp
        $uniqueId = uniqid() . '-' . now()->timestamp;
        // nama file asli tanpa extension dijadiin slug + unik id + ekstensinya tadi
        $newFilename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . $uniqueId . '.' . $file->getClientOriginalExtension();
        // Simpan file dengan nama baru
        $path = $file->storeAs($baseDirectory, $newFilename, 'public');
        return ['path' => $path];
    }

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

    // buat nampilin halaman apply sertifikasi di sisi asesi, di sini asesi harus mengisi form dgn berbagai data yg dibutuhkan
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
    // buat nampilin halaman rincian sertifikasi yg udh didaftar oleh asesi
    public function show_applied_sertifikasi($sert_id, $asesi_id, Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $asesi = Asesi::with('asesiattachmentfiles', 'transaction')->find($asesi_id);
        // dd($student);
        return view('asesi.sertifikasi.show-applied-page', [
            'sertification' => Sertification::with('skema')->find($sert_id),
            'student' => $student,
            'asesi' => $asesi
        ]);
    }
    // buat nampilin halaman edit sertifikasi yg udh didaftar asesi
    public function edit_apply_sertifikasi($sert_id, $asesi_id, Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $asesi = Asesi::with('asesiattachmentfiles')->find($asesi_id);
        // dd($asesi->asesiasesmenfile());
        return view('asesi.sertifikasi.edit-apply-page', [
            'sertification' => Sertification::with('skema')->find($sert_id),
            'student' => $student,
            'asesi' => $asesi

        ]);
    }

    //buat nampilin daftar asesi yg udh daftar suatu sertifikasi di sisi admin
    public function list_asesi($id, Request $request)
    {
        // dd($student);
        $sertification = Sertification::with('skema', 'asesi')->find($id);
        return view('admin.sertifikasi.pendaftar.indexpendaftar', [
            'sertification' => $sertification,
        ]);
    }

    //buat nampilin rincian data asesi yg udh daftar suatu sertifikasi di sisi admin
    public function rincian_data_asesi($id, Request $request)
    {
        $asesi = Asesi::with('student', 'transaction')->find($id);
        // dd($asesi->transaction);
        $sertification = $asesi->sertification;
        return view('admin.sertifikasi.pendaftar.rincianpendaftar', [
            'asesi' => $asesi,
            'sertification' => $sertification
        ]);
    }

    //buat memperbaharui status asesi (lanjutin ke asesmen atau ditolak) di sisi admin
    public function updateStatus($id, $sertification_id, Request $request)
    {
        $asesi = Asesi::with('sertification')->find($id);

        // Memperbarui status sesuai dengan yang diterima dari form
        $asesi->status = $request->status;
        $asesi->save();
        // dd(route('rincian_data_asesi', ['id' => $id]));
        return redirect()->back()->with('success', 'Status berhasil diperbarui');
    }

    // NOT USED ANYMORE
    //buat nampilin halaman rincian pra asesmen di sisi admin, NOT USED ANYMORE
    // public function rincian_praasesmen($id, Request $request)
    // {
    //     // dd($id);
    //     $sertification = Sertification::with('praasesmenfile')->find($id);
    //     return view('admin.sertifikasi.praasesmen.indexpraasesmen', [
    //         'sertification' => $sertification,
    //     ]);
    // }
    // NOT USED ANYMORE
    //buat update rincian pra asesmen di sisi admin, NOT USED ANYMORE
    // public function update_rincian_praasesmen($id, Request $request)
    // {
    //     // dd($request);
    //     $validated = $request->validate([
    //         'rincian_praasesmen' => 'required|string',
    //     ]);

    //     $sertification = Sertification::find($id);
    //     $sertification->rincian_praasesmen = $request->rincian_praasesmen;
    //     $sertification->save();
    //     // Simpan file baru
    //     if ($request->hasFile('praasesmen_attachment_file')) {
    //         foreach ($request->file('praasesmen_attachment_file') as $file) {
    //             if ($file->isValid()) {
    //                 $path = $this->storeFileWithUniqueName($request->file('praasesmen_attachment_file'), 'praasesmen_attachment_file');
    //                 Praasesmenfile::create([
    //                     'sertification_id' => $id,
    //                     'path_file' => $path['path'],
    //                 ]);
    //             }
    //         }
    //     }

    //     return redirect()->back()->with('success', 'Data rincian praasesmen berhasil disimpan!');
    // }
    // NOT USED ANYMORE
    // public function ajaxDeletePraasesmenFile(Request $request)
    // {
    //     $fileId = $request->getContent(); // body request berisi ID file (plain text)
    //     if (empty($fileId)) {
    //         return response()->json(['error' => 'File ID tidak valid.'], 400);
    //     }

    //     $file = Praasesmenfile::find($fileId);
    //     if ($file) {
    //         // Hapus file fisik
    //         if (Storage::disk('public')->exists($file->path_file)) {
    //             Storage::disk('public')->delete($file->path_file);
    //         }
    //         // Hapus record database
    //         $file->delete();
    //         return response()->json(['success' => true]);
    //     } else {
    //         return response()->json(['error' => 'File tidak ditemukan.'], 404);
    //     }
    // }

    //buat nampilin halaman daftar pengumuman asesmen di sisi admin/asesor sekaligus untuk buat nambah pengumuman
    public function daftar_pengumuman_asesmen($id, Request $request)
    {
        // dd($id);
        $sertification = Sertification::with('pengumumanasesmen')->find($id);
        return view('admin.sertifikasi.pengumuman.indexpengumuman', [
            'pengumumans' => $sertification->pengumumanasesmen,
            'sertification' => $sertification,
        ]);
    }
    //buat update rincian pra asesmen di sisi admin, NOT USED ANYMORE
    public function store_pengumuman_asesmen($id, Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'rincian_pengumuman_asesmen' => 'required|string',
            'sertification_id' => 'required'
        ]);

        $pengumumanAsesmen = Pengumumanasesmen::create(
            $validatedData
        );
        // Simpan file baru
        if ($request->hasFile('pengumuman_asesmen_attachment_file')) {
            foreach ($request->file('pengumuman_asesmen_attachment_file') as $file) {
                if ($file->isValid()) {
                    $path = $this->storeFileWithUniqueName($request->file('pengumuman_asesmen_attachment_file'), 'pengumuman_asesmen_attachment_file');
                    Pengumumanasesmenfile::create([
                        'pengumumanasesmen_id' => $pengumumanAsesmen->id,
                        'path_file' => $path['path'],
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Berhasil membuat pengumuman');
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
        // dd($pengumumanAsesmen);
        $pengumumanAsesmen->save();
        // Simpan file baru
        if ($request->hasFile('pengumuman_asesmen_attachment_file')) {
            foreach ($request->file('pengumuman_asesmen_attachment_file') as $file) {
                if ($file->isValid()) {
                    $path = $this->storeFileWithUniqueName($request->file('pengumuman_asesmen_attachment_file'), 'pengumuman_asesmen_attachment_file');
                    Pengumumanasesmenfile::create([
                        'pengumumanasesmen_id' => $pengumumanAsesmen->id,
                        'path_file' => $path['[path'],
                    ]);
                }
            }
        }

        return redirect(route('admin.sertification.assessment-announcement.index',$id))->with('success', 'Pengumuman berhasil diupdate berhasil disimpan!');
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
    public function destroy_pengumuman_asesmen(Request $request, $peng_id)
    {
        $pengumuman = Pengumumanasesmen::with('pengumumanasesmenfile')->find($peng_id);
        foreach ($pengumuman->pengumumanasesmenfile as $file) {
            // Cek apakah file benar-benar ada di storage sebelum menghapus
            if (Storage::disk('public')->exists($file->path_file)) {
                Storage::disk('public')->delete($file->path_file);
            }
            // Record file di database akan terhapus otomatis karena cascade delete
        }
        $pengumuman->delete();
        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus');
    }
    //buat nampilin halaman daftar pengumuman asesmen di sisi admin/asesor sekaligus untuk buat nambah pengumuman
    public function rincian_asesmen($id, Request $request)
    {
        // dd($id);
        return view('admin.sertifikasi.asesmen.indexasesmen', [
            'sertification' => Sertification::find($id)
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
        $sertification->save();
        // Simpan file baru
        if ($request->hasFile('asesmen_attachment_file')) {
            foreach ($request->file('asesmen_attachment_file') as $file) {
                if ($file->isValid()) {
                    $path = $this->storeFileWithUniqueName($request->file('asesmen_attachment_file'), 'asesmen_attachment_file');
                    Tugasasesmenattachmentfile::create([
                        'sertification_id' => $id,
                        'path_file' => $path['[path'],
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }
    // fungsi ajax buat hapus file dari tugas asesmen
    public function ajaxDeleteTugasAsesmenFile(Request $request)
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
    public function rincian_pembayaran($id, Request $request)
    {
        // dd($id);
        return view('admin.sertifikasi.pembayaran.indexpembayaran', [
            'sertification' => Sertification::find($id)
        ]);
    }
    public function update_rincian_pembayaran($id, Request $request)
    {
        // dd($id);
        return view('admin.sertifikasi.pembayaran.indexpembayaran', [
            'sertification' => Sertification::find($id)
        ]);
    }
    //NOT USED ANYMORE
    //buat nampilin halaman rincian pra asesmen di sisi asesi, NOT USED ANYMORE
    // public function rincian_praasesmen_asesi($id, Request $request)
    // {
    //     // dd($id);
    //     return view('asesi.sertifikasi.praasesmen.asesi-index-praasesmen', [
    //         'sertification' => Sertification::find($id)
    //     ]);
    // }
    //buat nampilin halaman rincian asesmen di sisi asesi
    public function rincian_asesmen_asesi($id, Request $request)
    {
        // dd($id);
        return view('asesi.sertifikasi.asesmen.asesi-index-asesmen', [
            'sertification' => Sertification::find($id)
        ]);
    }
    //buat nampilin halaman rincian pembayaran di sisi asesi
    public function rincian_bayar_asesi($sert_id, $asesi_id, Request $request)
    {
        // dd($request);
        return view('asesi.sertifikasi.bayar.indexbayar', [
            'sertification' => Sertification::with('asesor', 'skema')->find($sert_id),
            'asesi' => Asesi::with('student')->find($asesi_id)
        ]);
    }
    //buat nampilin halaman rincian laporan sertifikasi dari sisi admin
    public function rincian_laporan($sert_id, Request $request)
    {
        // dd($request);

        return view('admin.sertifikasi.laporansertifikasi', [
            'sertification' => Sertification::with('asesor', 'skema', 'asesi')->find($sert_id)
        ]);
    }

    //fungsi ajax buat memfilter riwayat sertifikasi
    public function filter_riwayat_sertifikasi(Request $request)
    {
        $filter = $request->input('filter');

        $query = Sertification::with('skema')->where('status', 'selesai');

        switch ($filter) {
            case 'bulan_ini':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case '3_bulan':
                $query->where('created_at', '>=', now()->subMonths(3));
                break;
            case 'tahun_ini':
                $query->whereYear('created_at', now()->year);
                break;
            default:
                // 'semua' - no additional filter
                break;
        }

        $sertifications = $query->get();

        return response()->json([
            'sertifications' => $sertifications
        ]);
    }
}
