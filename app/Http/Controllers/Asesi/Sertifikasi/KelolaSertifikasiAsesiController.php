<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Models\Asesi;
use App\Models\Student;
use App\Models\User;
use App\Models\Makulnilai;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use App\Models\Asesiattachmentfiles;
use App\Models\Studentattachmentfile;
use App\Notifications\PendaftarBaru;
use Illuminate\Support\Facades\Notification;

class KelolaSertifikasiAsesiController extends Controller
{

     public function asesi_daftar_sertifikasi(Request $request){
        // 1. Ambil user yang sedang login
        $user = $request->user();
        $student = $user->student;

        // 2. Ambil semua data pendaftaran (asesi) milik student tersebut.
        // Gunakan keyBy('sertification_id') agar mudah dicari di view.
        $asesi = Asesi::where('student_id', $student->id)
                      ->get()
                      ->keyBy('sertification_id');
        return view('asesi.sertifikasi.kelolasertifikasi.asesi-daftar-sertifikasi', [
            'sertifications' => Sertification::with('skema')->get(),
            'asesi' => $asesi
        ]);
     }

    public function form_daftar_sertifikasi($id, Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        // dd($student);
        // Ambil semua asesi milik student
        // $asesiBySertificationId = $student->asesi->keyBy('sertification_id');
        return view('asesi.sertifikasi.kelolasertifikasi.apply-page', [
            'sertification' => Sertification::with('skema')->find($id),
            'student' => $student,
            'user' => $user,
        ]);
    }

    public function submit_form_daftar_sertifikasi(Request $request)
    {
        // dd($request);
        $student = Student::findOrFail($request->student_id);
        $request->validate([
            'sertification_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'tmpt_lhr' => 'required|string|max:255',
            'tgl_lhr' => 'required|string|max:255',
            'kelamin' => 'required|string|max:255',
            'kebangsaan' => 'required|string|max:255',
            'no_tlp_hp' => 'required|string|max:255',
            'kualifikasi_pendidikan' => 'required|string|max:255',
            'tujuan_sert' => 'required|string|max:255',
            // 'makul_nilai' => 'required|string|max:255',
            'nama_makul' => 'required|array',
            'nama_makul.*' => 'required|string|max:255',
            'nilai_makul' => 'required|array',
            'nilai_makul.*' => 'required|string|max:10',
            'apl_1' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'apl_2' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktm' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pas_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',

            // validasi untuk input file multiple standar
            'kartu_hasil_studi' => 'nullable|array|max:5', // Memastikan input adalah array dan maksimal 5 item
            'kartu_hasil_studi.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072', // Validasi setiap file dalam array (3MB)
            'surat_ket_magang' => 'nullable|array|max:5', // Memastikan input adalah array dan maksimal 5 item
            'surat_ket_magang.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072', // Validasi setiap file dalam array (3MB)
            'sertif_pelatihan' => 'nullable|array|max:5',
            'sertif_pelatihan.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072', // 3MB
            'dok_pendukung_lain' => 'nullable|array|max:5',
            'dok_pendukung_lain.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB
        ]);
        $student->fill($request->only([
            'nik',
            'name',
            'tmpt_lhr',
            'tgl_lhr',
            'kelamin',
            'kebangsaan',
            'no_tlp_rmh',
            'no_tlp_kntr',
            'no_tlp_hp',
            'kualifikasi_pendidikan',
        ]));
        foreach (['foto_ktp', 'foto_ktm', 'pas_foto'] as $fileField) {
            if ($request->hasFile($fileField)) {
                // Cek jika file sebelumnya ada (tidak null atau kosong)
                if ($student->$fileField && Storage::disk('public')->exists($student->$fileField)) {
                    // Hapus file lama jika ada
                    Storage::disk('public')->delete($student->$fileField);
                }
                // Simpan file baru
                $fileData = FileHelper::storeFileWithUniqueName($request->file($fileField), $fileField); // $fileField sebagai baseDirectory
                $student->$fileField = $fileData['path'];
            }
        }
        if ($student->isDirty()) {
            $student->save();
        }
        $asesiData = [
            'student_id' => $student->id,
            'sertification_id' => $request->input('sertification_id'),
            'tujuan_sert' => $request->input('tujuan_sert'),
            // 'makul_nilai' => $request->input('makul_nilai'),
        ];
        foreach (['apl_1', 'apl_2'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $fileData = FileHelper::storeFileWithUniqueName($request->file($fileField), 'asesi_apl_files');
                $asesiData[$fileField] = $fileData['path'];
            }
        }
        $asesi = Asesi::create($asesiData);

        // buat nyimpan mata kuliah dan nilainya
        $namaMakuls = $request->input('nama_makul');
        $nilaiMakuls = $request->input('nilai_makul');
        // as $index itu ngambil indexnya, 0, 1, 2 dst
        foreach ($namaMakuls as $index => $namaMakul) {
            // !empty artinya ngecek apakah tidak null, false, 0, '0', [], atau ''
            // isset ngecek apa null atau tidak
            if (!empty($namaMakul) && isset($nilaiMakuls[$index])) {
                MakulNilai::create([
                    'asesi_id' => $asesi->id,
                    'nama_makul' => $namaMakul,
                    'nilai_makul' => $nilaiMakuls[$index],
                ]);
            }
        }
        // buat input multiple standar
        $fileTypes = ['surat_ket_magang', 'sertif_pelatihan', 'dok_pendukung_lain'];

        foreach ($fileTypes as $fileType) {
            if ($request->hasFile($fileType)) { // Cek apakah ada file yang diunggah untuk field ini
                foreach ($request->file($fileType) as $file) {
                    $fileData = FileHelper::storeFileWithUniqueName($file, "asesi_attachments");

                    Asesiattachmentfiles::create([
                        'asesi_id' => $asesi->id,
                        'type' => $fileType,
                        'path_file' => $fileData['path'],
                    ]);
                }
            }
        }
        if ($request->hasFile('kartu_hasil_studi')) {
            // 1. Hapus file lama dari DB dan storage untuk tipe ini
            $oldFiles = Studentattachmentfile::where('student_id', $student->id)
                ->get();
            foreach ($oldFiles as $oldFile) {
                Storage::disk('public')->delete($oldFile->path_file);
                $oldFile->delete();
            }
            foreach ($request->file('kartu_hasil_studi') as $file) {
                $fileData = FileHelper::storeFileWithUniqueName($file, "student_attachments");
                Studentattachmentfile::create([
                    'student_id' => $student->id,
                    'type' => 'kartu_hasil_studi',
                    'path_file' => $fileData['path'],
                ]);
            }
        }
        $admins = User::role('admin')->get();
        Notification::send($admins, new PendaftarBaru($asesi));
        $sertification_id = $request->input('sertification_id');
        return redirect(route('asesi.sertifikasi.applied.show',[$sertification_id, $asesi->id]))->with('Success', 'Berhasil daftar sertifikasi');
    }

    public function detail_applied_sertifikasi($sert_id, $asesi_id, Request $request)
    {
        $user = $request->user();
        $student = $user->student()->with('studentattachmentfile')->first();
        $asesi = Asesi::with('asesiattachmentfiles', 'transaction','makulnilais','sertifikat')->find($asesi_id);
        // dd($student);
        return view('asesi.sertifikasi.kelolasertifikasi.show-applied-page', [
            'sertification' => Sertification::with('skema')->find($sert_id),
            'student' => $student,
            'asesi' => $asesi
        ]);
    }

    public function edit_applied_sertifikasi($sert_id, $asesi_id, Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $asesi = Asesi::with('asesiattachmentfiles')->find($asesi_id);
        // dd($asesi->asesiasesmenfile());
        return view('asesi.sertifikasi.kelolasertifikasi.edit-apply-page', [
            'sertification' => Sertification::with('skema')->find($sert_id),
            'student' => $student,
            'asesi' => $asesi

        ]);
    }

    public function update_applied_sertifikasi($sert_id, $asesi_id, Request $request)
    {
        // dd($request);
        $student = Student::findOrFail($request->student_id);
        $asesi = Asesi::findOrFail($asesi_id);
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'tmpt_lhr' => 'required|string|max:255',
            'tgl_lhr' => 'required|string|max:255',
            'kelamin' => 'required|string|max:255',
            'kebangsaan' => 'required|string|max:255',
            'no_tlp_hp' => 'required|string|max:255',
            'kualifikasi_pendidikan' => 'required|string|max:255',
            'tujuan_sert' => 'required|string|max:255',
            // 'makul_nilai' => 'required|string|max:255',
            // Tambahkan validasi untuk array
            'makul_nama' => 'required|array',
            'makul_nama.*' => 'required|string|max:255',
            'makul_nilai' => 'required|array',
            'makul_nilai.*' => 'required|string|max:10',
            'apl_1' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'apl_2' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktm' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            // 'foto_khs' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pas_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            // validasi multiple input standar
            'kartu_hasil_studi' => 'nullable|array|max:5', // Memastikan input adalah array dan maksimal 5 item
            'kartu_hasil_studi.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072', // Validasi setiap file dalam array (3MB)
            'surat_ket_magang' => 'nullable|array|max:5',
            'surat_ket_magang.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'sertif_pelatihan' => 'nullable|array|max:5',
            'sertif_pelatihan.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'dok_pendukung_lain' => 'nullable|array|max:5',
            'dok_pendukung_lain.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);
        // untuk tabel student
        $student->fill($request->only([
            'nik',
            'name',
            'tmpt_lhr',
            'tgl_lhr',
            'kelamin',
            'kebangsaan',
            'no_tlp_rmh',
            'no_tlp_kntr',
            'no_tlp_hp',
            'kualifikasi_pendidikan',
        ]));

        foreach (['foto_ktp', 'foto_ktm', 'pas_foto'] as $fileField) {
            // cek di inputannya ada atau tidak
            if ($request->hasFile($fileField)) {
                // kalau ada, cek lagi di storage file sebelumnya ada atau tidak
                if ($student->$fileField && Storage::disk('public')->exists($student->$fileField)) {
                    // kalau ada, hapus yg lama
                    Storage::disk('public')->delete($student->$fileField);
                }
                // simpan file yang baru di penyimpanan, simpan juga path dari filenya
                $fileData = FileHelper::storeFileWithUniqueName($request->file($fileField), $fileField); // $fileField sebagai baseDirectory
                $student->$fileField = $fileData['path'];
            }
        }
        if ($student->isDirty()) {
            $student->save();
        }
        // untuk tabel asesi
        $asesi->fill($request->only([
            'tujuan_sert',
        ]));
        foreach (['apl_1', 'apl_2'] as $fileField) {
            if ($request->hasFile($fileField)) {
                if ($asesi->$fileField && Storage::disk('public')->exists($asesi->$fileField)) {
                    Storage::disk('public')->delete($asesi->$fileField);
                }
                $fileData = FileHelper::storeFileWithUniqueName($request->file($fileField), 'asesi_apl_files');
                $asesi->$fileField = $fileData['path'];
            }
        }
        if ($asesi->isDirty()) {
            $asesi->save();
        }
        // untuk update data makulnilai
        // 1. Hapus semua record MakulNilai yang lama terkait dengan asesi ini.
        MakulNilai::where('asesi_id', $asesi->id)->delete();

        // 2. Ambil data baru dari request.
        $makulNamas = $request->input('makul_nama');
        $makulNilais = $request->input('makul_nilai');

        // 3. Buat ulang record MakulNilai berdasarkan data baru (logika yang sama seperti di store).
        if (is_array($makulNamas)) {
            foreach ($makulNamas as $index => $namaMakul) {
                if (!empty($namaMakul) && isset($makulNilais[$index])) {
                    MakulNilai::create([
                        'asesi_id' => $asesi->id,
                        'nama_makul' => $namaMakul,
                        'nilai_makul' => $makulNilais[$index],
                    ]);
                }
            }
        }
        //untuk input multiple
        $fileTypes = ['surat_ket_magang', 'sertif_pelatihan', 'dok_pendukung_lain'];
        foreach ($fileTypes as $fileType) {
            if ($request->hasFile($fileType)) { // Jika ada file baru yang diunggah untuk tipe ini
                // 1. Hapus file lama dari DB dan storage untuk tipe ini
                $oldFiles = Asesiattachmentfiles::where('asesi_id', $asesi->id)
                    ->where('type', $fileType)
                    ->get();
                foreach ($oldFiles as $oldFile) {
                    Storage::disk('public')->delete($oldFile->path_file);
                    $oldFile->delete();
                }

                // 2. Simpan file baru
                foreach ($request->file($fileType) as $file) {
                    $fileData = FileHelper::storeFileWithUniqueName($file, "asesi_attachments");
                    Asesiattachmentfiles::create([
                        'asesi_id' => $asesi->id,
                        'type' => $fileType,
                        'path_file' => $fileData['path'],
                    ]);
                }
            }
        }

        // nyimpan kartu hasil studi
        if ($request->hasFile('kartu_hasil_studi')) {
            // 1. Hapus file lama dari DB dan storage untuk tipe ini
            $oldFiles = Studentattachmentfile::where('student_id', $student->id)
                ->get();
            foreach ($oldFiles as $oldFile) {
                Storage::disk('public')->delete($oldFile->path_file);
                $oldFile->delete();
            }
            foreach ($request->file('kartu_hasil_studi') as $file) {
                $fileData = FileHelper::storeFileWithUniqueName($file, "student_attachments");
                Studentattachmentfile::create([
                    'student_id' => $student->id,
                    'path_file' => $fileData['path'],
                ]);
            }
        }



        return redirect()->back()->with('success', 'Berhasil update data sertifikasi');
        // return redirect('/dashboard')->with('Success', 'Berhasil update data sertifikasi');
    }
}
