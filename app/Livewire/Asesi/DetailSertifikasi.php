<?php

namespace App\Livewire\Asesi;

use App\Models\Asesi;
use App\Models\Asesiattachmentfile;
use App\Models\Makulnilai;
use App\Models\Studentattachmentfile;
use App\Models\Sertification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Helpers\FileHelper;
use App\Http\Controllers\NotificationController;
use App\Notifications\PendaftarBaru;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class DetailSertifikasi extends Component
{
    use WithFileUploads;

    // Data dari URL
    public Sertification $sertification;
    public Asesi $asesi;

    // State
    public bool $isEditing = false;

    // Model terkait
    public User $user;
    public Student $student;

    // Properti Form (sama seperti ApplySertifikasi)
    public $name, $nik, $tmpt_lhr, $tgl_lhr, $kelamin, $kebangsaan, $no_tlp_hp, $kualifikasi_pendidikan;
    public $tujuan_sert;
    public $makulNilais = [['nama_makul' => '', 'nilai_makul' => '']];
    public $apl_1, $apl_2, $foto_ktp, $foto_ktm, $pas_foto;
    public $kartu_hasil_studi = [], $surat_ket_magang = [], $sertif_pelatihan = [], $dok_pendukung_lain = [];

    protected function rules()
    {
        // Untuk update, semua file yang bisa diganti harus 'nullable'.
        // Logika dinamis untuk KHS tetap sama dan sudah benar.
        $hasExistingKhs = Studentattachmentfile::where('student_id', $this->student->id)
            ->where('type', 'kartu_hasil_studi')
            ->exists();

        return [
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'tgl_lhr' => 'required|date',
            'tujuan_sert' => 'required|string',
            'makulNilais.*.nama_makul' => 'required|string',
            'makulNilais.*.nilai_makul' => 'required|string',
            
            // File utama sekarang nullable karena mungkin sudah ada
            'apl_1' => 'nullable|file|mimes:doc,docx,pdf|max:2048',
            'apl_2' => 'nullable|file|mimes:doc,docx,pdf|max:3072',

            'foto_ktp' => 'nullable|file|image|max:1024',
            'foto_ktm' => 'nullable|file|image|max:1024',
            'pas_foto' => 'nullable|file|image|max:1024',
            
            'kartu_hasil_studi' => ($hasExistingKhs ? 'nullable' : 'required') . '|array|max:5',
            'kartu_hasil_studi.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',

            'surat_ket_magang' => 'nullable|array|max:5', 
            'surat_ket_magang.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072', 
            'sertif_pelatihan' => 'nullable|array|max:5',
            'sertif_pelatihan.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072', 
            'dok_pendukung_lain' => 'nullable|array|max:5',
            'dok_pendukung_lain.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ];
    }

    public function mount($sert_id, $asesi_id, Request $request)
    {
        NotificationController::markAsRead($request);
        $this->sertification = Sertification::with('skema')->findOrFail($sert_id);
        $this->asesi = Asesi::with('makulnilais', 'asesiattachmentfiles')->findOrFail($asesi_id);
        $this->user = Auth::user();
        $this->student = $this->user->student;

        $this->name = $this->user->name;
        $this->no_tlp_hp = $this->user->no_tlp_hp;
        $this->nik = $this->student->nik;
        $this->tmpt_lhr = $this->student->tmpt_lhr;
        $this->tgl_lhr = $this->student->tgl_lhr;
        $this->kelamin = $this->student->kelamin;
        $this->kebangsaan = $this->student->kebangsaan;
        $this->kualifikasi_pendidikan = $this->student->kualifikasi_pendidikan;
        $this->tujuan_sert = $this->asesi->tujuan_sert;
        if ($this->asesi->makulnilais->isNotEmpty()) {
            $this->makulNilais = $this->asesi->makulnilais->map->only(['nama_makul', 'nilai_makul'])->toArray();
        }
    } 

    public function addMakul()
    {
        $this->makulNilais[] = ['nama_makul' => '', 'nilai_makul' => ''];
    }

    public function removeMakul($index)
    {
        unset($this->makulNilais[$index]);
        $this->makulNilais = array_values($this->makulNilais);
    }

    public function enterEditMode()
    {
        $this->isEditing = true;
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->resetErrorBag(); // Hapus error validasi jika ada
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            // Logika penyimpanan dari controller (hanya untuk create)
            $this->user->update(['name' => $this->name, 'no_tlp_hp' => $this->no_tlp_hp]);
            $this->student->update([
                'nik' => $this->nik,
                'tmpt_lhr' => $this->tmpt_lhr,
                'tgl_lhr' => $this->tgl_lhr,
                'kelamin' => $this->kelamin,
                'kebangsaan' => $this->kebangsaan,
                'kualifikasi_pendidikan' => $this->kualifikasi_pendidikan,
            ]);
            $studentFilesData = [];
            foreach (['foto_ktp', 'foto_ktm', 'pas_foto'] as $fileField) {
                if ($this->$fileField) {
                    if ($this->student->$fileField && Storage::disk('public')->exists($this->student->$fileField)) {
                        Storage::disk('public')->delete($this->student->$fileField);
                    }
                    $studentFilesData[$fileField] = FileHelper::storeFileWithUniqueName($this->$fileField, 'student_files')['path'];
                }
            }
            if (!empty($studentFilesData)) {
                $this->student->update($studentFilesData); // Hanya 1 UPDATE query
            }


            $asesiData = ['tujuan_sert' => $this->tujuan_sert];
            if ($this->apl_1) {
                if ($this->asesi->apl_1) Storage::disk('public')->delete($this->asesi->apl_1);
                $asesiData['apl_1'] = FileHelper::storeFileWithUniqueName($this->apl_1, 'asesi_files')['path'];
            }
            if ($this->apl_2) {
                if ($this->asesi->apl_2) Storage::disk('public')->delete($this->asesi->apl_2);
                $asesiData['apl_2'] = FileHelper::storeFileWithUniqueName($this->apl_2, 'asesi_files')['path'];
            }
            $this->asesi->update($asesiData);
            $this->asesi->makulnilais()->delete();
            $makulData = [];
            $now = now();
            foreach ($this->makulNilais as $makul) {
                if (!empty($makul['nama_makul']) && !empty($makul['nilai_makul'])) {
                    $makulData[] = [
                        'asesi_id' => $this->asesi->id,
                        'nama_makul' => $makul['nama_makul'],
                        'nilai_makul' => $makul['nilai_makul'],
                        'created_at' => $now, 'updated_at' => $now,
                    ];
                }
            }
            if (!empty($makulData)) {
                Makulnilai::insert($makulData);
            }

            // buat input multiple standar
            $this->handleMultipleUploads('kartu_hasil_studi', $this->student, $this->asesi);
            $this->handleMultipleUploads('surat_ket_magang', $this->student, $this->asesi);
            $this->handleMultipleUploads('sertif_pelatihan', $this->student, $this->asesi);
            $this->handleMultipleUploads('dok_pendukung_lain', $this->student, $this->asesi);
        });

        $this->isEditing = false; // Kembali ke mode 'show'
        $this->dispatch('notify', message: 'Data berhasil diperbarui!');
    }

    private function handleMultipleUploads(string $type, Student $student, Asesi $asesi)
    {
        // Cek apakah ada file baru yang di-upload untuk tipe ini
        if (isset($this->{$type}) && !empty($this->{$type})) {
            $model = ($type === 'kartu_hasil_studi') ? $student : $asesi;
            $attachmentModelClass = ($type === 'kartu_hasil_studi') ? Studentattachmentfile::class : Asesiattachmentfile::class;
            $foreignKey = ($type === 'kartu_hasil_studi') ? 'student_id' : 'asesi_id';

            // 1. Ambil path file lama (1 SELECT)
            $oldFilePaths = $attachmentModelClass::where($foreignKey, $model->id)->where('type', $type)->pluck('path_file');

            // 2. Hapus semua record lama (1 DELETE)
            if ($oldFilePaths->isNotEmpty()) {
                $attachmentModelClass::where($foreignKey, $model->id)->where('type', $type)->delete();
                // 3. Hapus semua file fisik (1 Panggilan Storage)
                Storage::disk('public')->delete($oldFilePaths->all());
            }

            // 4. Siapkan data baru dan simpan (1 INSERT)
            $newFilesData = [];
            $now = now();
            foreach ($this->{$type} as $file) {
                $newFilesData[] = [
                    $foreignKey => $model->id,
                    'type' => $type,
                    'path_file' => FileHelper::storeFileWithUniqueName($file, 'asesi_files')['path'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (!empty($newFilesData)) {
                $attachmentModelClass::insert($newFilesData);
            }
        }
    }

    public function render()
    {
        return view('livewire.asesi.detail-sertifikasi');
    }
}
