<?php

namespace App\Http\Controllers;

use App\Models\Asesi;
use App\Models\Asesmenfile;
use App\Models\Praasesmenfile;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ManageCertificationController extends Controller
{
    // public function getAsesor($skemaId)
    // {
    //     $asesors = Skema::find($skemaId)->asesors;
    //     return response()->json(['asesor' => $asesors]);
    // }
    // AsesiController.php

    private function storeFileWithUniqueName(UploadedFile $file, string $baseDirectory): array
    {
        //versi edoxid
        // $folder = uniqid().'-'.now()->timestamp;
        // $file_name = $student->unique_id.'-'.Str::slug($student->name).'-'.$type->slug.'-'.Str::slug($format_required_file->name).'-'.uniqid().'.'.$request->file($format_required_file->id)->getClientOriginalExtension();
        // $request->file($format_required_file->id)->storeAs('submissions/tmp/'.$folder, $file_name, 'public')

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
        $sertification = Sertification::with('skema', 'asesi')->find($id);
        return view('admin.sertifikasi.pendaftar.indexpendaftar', [
            'sertification' => $sertification,
        ]);
    }

    //buat nampilin rincian data asesi di sisi admin
    public function rincian_data_asesi($id, Request $request)
    {
        $asesi = Asesi::with('student')->find($id);
        $sertification = $asesi->sertification;
        return view('admin.sertifikasi.pendaftar.rincianpendaftar', [
            'asesi' => $asesi,
            'sertification' => $sertification
        ]);
    }

    //buat memperbaharui status asesi di sisi admin
    public function updateStatus($id, $sertification_id, Request $request)
    {
        $asesi = Asesi::find($id);

        // Memperbarui status sesuai dengan yang diterima dari form
        $asesi->status = $request->status;
        $asesi->save();
        $dataTransaksi = [
            'asesi_id' => $asesi->id,
            'status' => 'belum bayar',
            'tipe' => 'manual',
        ];
        Transaction::create($dataTransaksi);
        // dd(route('rincian_data_asesi', ['id' => $id]));
        return redirect()->back()->with('success', 'Status berhasil diperbarui');
    }
    public function updateStatusBayar($id, Request $request)
    {
        $asesi = Asesi::find($id);
        $transaction = $asesi->transaction;
        // Memperbarui status sesuai dengan yang diterima dari form
        $transaction->status = $request->status;
        $transaction->save();
        return redirect()->back()->with('success', 'Status berhasil diperbarui');
    }
    //buat nampilin halaman rincian pra asesmen di sisi admin
    public function rincian_praasesmen($id, Request $request)
    {
        // dd($id);
        $sertification = Sertification::with('praasesmenfile')->find($id);
        return view('admin.sertifikasi.praasesmen.indexpraasesmen', [
            'sertification' => $sertification,
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
        // Hapus file lama yang dicentang
        // if ($request->has('delete_files')) {
        //     foreach ($request->input('delete_files') as $fileId) {
        //         $file = Praasesmenfile::find($fileId);
        //         if ($file) {
        //             Storage::disk('public')->delete($file->path_file);
        //             $file->delete();
        //         }
        //     }
        // }
        // Simpan file baru
        if ($request->hasFile('praasesmen_attachment_file')) {
            foreach ($request->file('praasesmen_attachment_file') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('praasesmen_attachment_file', 'public');
                    Praasesmenfile::create([
                        'sertification_id' => $id,
                        'path_file' => $path,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Data rincian praasesmen berhasil disimpan!');
    }
    public function ajaxDeletePraasesmenFile(Request $request)
    {
        $fileId = $request->getContent(); // body request berisi ID file (plain text)
        if (empty($fileId)) {
            return response()->json(['error' => 'File ID tidak valid.'], 400);
        }

        $file = Praasesmenfile::find($fileId);
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
            'rincian_asesmen' => 'required|string',
        ]);

        $sertification = Sertification::find($id);
        $sertification->rincian_asesmen = $request->rincian_asesmen;
        $sertification->save();
        // Simpan file baru
        if ($request->hasFile('asesmen_attachment_file')) {
            foreach ($request->file('asesmen_attachment_file') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('asesmen_attachment_file', 'public');
                    Asesmenfile::create([
                        'sertification_id' => $id,
                        'path_file' => $path,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }
    public function ajaxDeleteAsesmenFile(Request $request)
    {
        $fileId = $request->getContent(); // body request berisi ID file (plain text)
        if (empty($fileId)) {
            return response()->json(['error' => 'File ID tidak valid.'], 400);
        }

        $file = Asesmenfile::find($fileId);
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
    public function rincian_bayar_asesi($sert_id, $asesi_id, Request $request)
    {
        // dd($request);
        return view('asesi.sertifikasi.bayar.indexbayar', [    
            'sertification' => Sertification::with('asesor','skema')->find($sert_id),
            'asesi' => Asesi::with('student')->find($asesi_id)
        ]);
    }
}
