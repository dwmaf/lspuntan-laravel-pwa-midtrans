<?php

namespace App\Http\Controllers;

use App\Models\Asesi;
use App\Models\Sertification;
use App\Models\Student;
use App\Models\Asesiattachmentfiles;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\UploadedFile;

class ApplyCertificationController extends Controller
{

    private function storeFileWithUniqueName(UploadedFile $file, string $baseDirectory): array
    {
        // id unik berdasarkan timestamp
        $uniqueId = uniqid().'-'.now()->timestamp;
        // nama file asli tanpa extension dijadiin slug + unik id + ekstensinya tadi
        $newFilename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . $uniqueId . '.' . $file->getClientOriginalExtension();
        // Simpan file dengan nama baru
        $path = $file->storeAs($baseDirectory, $newFilename, 'public');
        return ['path' => $path];
    }
    // fungsi buat nyimpan sertifikasi
    public function store(Request $request)
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
            'makul_nilai' => 'required|string|max:255',
            'apl_1' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'apl_2' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktm' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_khs' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pas_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',

            // validasi untuk input file multiple standar
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
        foreach (['foto_ktp', 'foto_ktm', 'foto_khs', 'pas_foto'] as $fileField) {
            if ($request->hasFile($fileField)) {
                // Cek jika file sebelumnya ada (tidak null atau kosong)
                if ($student->$fileField && Storage::disk('public')->exists($student->$fileField)) {
                    // Hapus file lama jika ada
                    Storage::disk('public')->delete($student->$fileField);
                }
                // Simpan file baru
                $fileData = $this->storeFileWithUniqueName($request->file($fileField), $fileField); // $fileField sebagai baseDirectory
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
            'makul_nilai' => $request->input('makul_nilai'),
        ];
        foreach (['apl_1', 'apl_2'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $fileData = $this->storeFileWithUniqueName($request->file($fileField), 'asesi_apl_files');
                $asesiData[$fileField] = $fileData['path'];
            }
        }
        $asesi = Asesi::create($asesiData);

        // buat input multiple standar tanpa dropzone
        $multipleFileFields = [
            'surat_ket_magang' => 'surat_ket_magang', // request_key => db_file_type
            'sertif_pelatihan' => 'sertif_pelatihan',
            'dok_pendukung_lain' => 'dok_pendukung_lain',
        ];

        foreach ($multipleFileFields as $requestKey => $fileTypeInDb) {
            if ($request->hasFile($requestKey)) { // Cek apakah ada file yang diunggah untuk field ini
                foreach ($request->file($requestKey) as $file) {
                    $fileData = $this->storeFileWithUniqueName($file, "asesi_attachments");

                    Asesiattachmentfiles::create([
                        'asesi_id' => $asesi->id,
                        'type' => $fileTypeInDb,
                        'path_file' => $fileData['path'],
                    ]);
                }
            }
        }

        return redirect(route('asesi.certifications.index'))->with('Success', 'Berhasil daftar sertifikasi');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update($asesi_id, Request $request)
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
            'makul_nilai' => 'required|string|max:255',
            'apl_1' => 'nullabel|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'apl_2' => 'nullabel|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktm' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_khs' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pas_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            // validasi multiple input standar
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

        foreach (['foto_ktp', 'foto_ktm', 'foto_khs', 'pas_foto'] as $fileField) {
            // cek di inputannya ada atau tidak
            if ($request->hasFile($fileField)) {
                // kalau ada, cek lagi di storage file sebelumnya ada atau tidak
                if ($student->$fileField && Storage::disk('public')->exists($student->$fileField)) {
                    // kalau ada, hapus yg lama
                    Storage::disk('public')->delete($student->$fileField);
                }
                // simpan file yang baru di penyimpanan, simpan juga path dari filenya
                $fileData = $this->storeFileWithUniqueName($request->file($fileField), $fileField); // $fileField sebagai baseDirectory
                $student->$fileField = $fileData['path'];
            }
        }
        if ($student->isDirty()) {
            $student->save();
        }
        // untuk tabel asesi
        $asesi->fill($request->only([
            'tujuan_sert',
            'makul_nilai',
        ]));
        foreach (['apl_1', 'apl_2'] as $fileField) {
            if ($request->hasFile($fileField)) {
                if ($asesi->$fileField && Storage::disk('public')->exists($asesi->$fileField)) {
                    Storage::disk('public')->delete($asesi->$fileField);
                }
                $fileData = $this->storeFileWithUniqueName($request->file($fileField), 'asesi_apl_files');
                $asesi->$fileField = $fileData['path'];
            }
        }
        if ($asesi->isDirty()) {
            $asesi->save();
        }
        //untuk input multiple
        foreach (['surat_ket_magang', 'sertif_pelatihan', 'dok_pendukung_lain'] as $requestKey => $fileTypeInDb) {
            if ($request->hasFile($requestKey)) { // Jika ada file baru yang diunggah untuk tipe ini
                // 1. Hapus file lama dari DB dan storage untuk tipe ini
                $oldFiles = Asesiattachmentfiles::where('asesi_id', $asesi->id)
                    ->where('type', $fileTypeInDb)
                    ->get();
                foreach ($oldFiles as $oldFile) {
                    Storage::disk('public')->delete($oldFile->path_file);
                    $oldFile->delete();
                }

                // 2. Simpan file baru
                foreach ($request->file($requestKey) as $file) {
                    $fileData = $this->storeFileWithUniqueName($file, "asesi_attachments");
                    Asesiattachmentfiles::create([
                        'asesi_id' => $asesi->id,
                        'type' => $fileTypeInDb,
                        'path_file' => $fileData['path'],
                    ]);
                }
            }
        }


        
        return redirect()->back()->with('success', 'Berhasil update data sertifikasi');
        // return redirect('/dashboard')->with('Success', 'Berhasil update data sertifikasi');
    }

}
