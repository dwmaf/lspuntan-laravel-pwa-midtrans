<?php

namespace App\Livewire\Asesi;

use App\Models\Asesi;
use App\Models\Asesiattachmentfile;
use App\Models\Makulnilai;
use App\Models\Studentattachmentfile;
use App\Models\Sertification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Helpers\FileHelper;
use App\Notifications\PendaftarBaru;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class ApplySertifikasi extends Component
{
    use WithFileUploads;

    // Data dari URL
    public Sertification $sertification;

    // Model terkait
    public User $user;
    public Student $student;

    // Properti Form
    public $name, $nik, $tmpt_lhr, $tgl_lhr, $kelamin, $kebangsaan, $no_tlp_hp, $kualifikasi_pendidikan;
    public $tujuan_sert;
    public $makulNilais = [['nama_makul' => '', 'nilai_makul' => '']];

    // Properti File
    public $apl_1, $apl_2, $foto_ktp, $foto_ktm, $pas_foto;
    public $kartu_hasil_studi = [], $surat_ket_magang = [], $sertif_pelatihan = [], $dok_pendukung_lain = [];

    public function mount($sertificationId)
    {
        $this->sertification = Sertification::with('skema')->findOrFail($sertificationId);
        $this->user = Auth::user();
        $this->student = $this->user->student;

        // Pre-fill form dengan data yang sudah ada
        $this->name = $this->user->name;
        $this->no_tlp_hp = $this->user->no_tlp_hp;
        $this->nik = $this->student->nik;
        $this->tmpt_lhr = $this->student->tmpt_lhr;
        $this->tgl_lhr = $this->student->tgl_lhr;
        $this->kelamin = $this->student->kelamin;
        $this->kebangsaan = $this->student->kebangsaan;
        $this->kualifikasi_pendidikan = $this->student->kualifikasi_pendidikan;
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

    protected function rules()
    {
        $hasExistingKhs = Studentattachmentfile::where('student_id', $this->student->id)
            ->where('type', 'kartu_hasil_studi')
            ->exists();

        // Kembalikan semua aturan, dengan aturan 'kartu_hasil_studi' yang sudah dinamis
        return [
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'tgl_lhr' => 'required|date',
            'tujuan_sert' => 'required|string',
            'makulNilais.*.nama_makul' => 'required|string',
            'makulNilais.*.nilai_makul' => 'required|string',
            'apl_1' => 'required|file|mimes:doc,docx,pdf|max:2048',
            'apl_2' => 'required|file|mimes:doc,docx,pdf|max:3072',
            'foto_ktp' => (!$this->student->foto_ktp ? 'required' : 'nullable') . '|file|image|max:1024',
            'foto_ktm' => (!$this->student->foto_ktm ? 'required' : 'nullable') . '|file|image|max:1024',
            'pas_foto' => (!$this->student->pas_foto ? 'required' : 'nullable') . '|file|image|max:1024',
            
            // Aturan dinamis: 'required' jika belum ada file, 'nullable' jika sudah ada.
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

    public function save()
    {
        // Validasi untuk form create
        $this->validate();

        $asesi = DB::transaction(function () {
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


            $newAsesi = Asesi::create([
                'student_id' => $this->student->id,
                'sertification_id' => $this->sertification->id,
                'tujuan_sert' => $this->tujuan_sert,
                'status' => 'daftar',
                'apl_1' => FileHelper::storeFileWithUniqueName($this->apl_1, 'asesi_files')['path'],
                'apl_2' => FileHelper::storeFileWithUniqueName($this->apl_2, 'asesi_files')['path'],
            ]);

            $makulData = [];
            $now = now();
            foreach ($this->makulNilais as $makul) {
                if (!empty($makul['nama_makul']) && !empty($makul['nilai_makul'])) {
                    $makulData[] = [
                        'asesi_id' => $newAsesi->id,
                        'nama_makul' => $makul['nama_makul'],
                        'nilai_makul' => $makul['nilai_makul'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            if (!empty($makulData)) {
                Makulnilai::insert($makulData);
            }

            // buat input multiple standar
            $this->handleMultipleUploads('kartu_hasil_studi', $this->student, $newAsesi);
            $this->handleMultipleUploads('surat_ket_magang', $this->student, $newAsesi);
            $this->handleMultipleUploads('sertif_pelatihan', $this->student, $newAsesi);
            $this->handleMultipleUploads('dok_pendukung_lain', $this->student, $newAsesi);
            $this->notifyAdmin($newAsesi);
            return $newAsesi;
        });

        return redirect()->route('asesi.sertifikasi.show', [$this->sertification->id, $asesi->id])
            ->with('success', 'Pendaftaran berhasil! Silahkan periksa kembali data Anda.');
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

    protected function notifyAdmin(Asesi $asesi)
    {
        $admins = User::role('admin')->get();
        Notification::send($admins, new PendaftarBaru($asesi));
    }
    public function render()
    {
        return view('livewire.asesi.apply-sertifikasi');
    }
}
