<?php

namespace App\Livewire\Asesi\Profile;

use App\Helpers\FileHelper;
use App\Models\Student;
use App\Models\Studentattachmentfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfileAsesi extends Component
{
    use WithFileUploads;

    // Properti untuk menampung model
    public User $user;
    public Student $student;

    // Properti untuk form fields (Data Pribadi)
    public string $name = '';
    public string $nik = '';
    public string $tmpt_lhr = '';
    public string $tgl_lhr = '';
    public string $kelamin = '';
    public string $kebangsaan = '';
    public ?string $no_tlp_rmh = null;
    public ?string $no_tlp_kntr = null;
    public string $no_tlp_hp = '';
    public string $kualifikasi_pendidikan = '';

    // Properti untuk form fields (Data Pekerjaan)
    public ?string $nama_institusi = null;
    public ?string $jabatan = null;
    public ?string $alamat_kantor = null;
    public ?string $no_tlp_email_fax = null;

    // Properti untuk file uploads
    public $foto_ktp;
    public $foto_ktm;
    public $pas_foto;
    public $kartu_hasil_studi = [];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'tmpt_lhr' => 'required|string|max:255',
            'tgl_lhr' => 'required|date',
            'kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'kebangsaan' => 'required|string|max:255',
            'no_tlp_hp' => 'required|string|max:255',
            'kualifikasi_pendidikan' => 'required|string|max:255',
            'no_tlp_rmh' => 'nullable|string|max:255',
            'no_tlp_kntr' => 'nullable|string|max:255',
            'nama_institusi' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'alamat_kantor' => 'nullable|string|max:255',
            'no_tlp_email_fax' => 'nullable|string|max:255',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktm' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pas_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'kartu_hasil_studi' => 'nullable|array|max:5',
            'kartu_hasil_studi.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
        ];
    }

    public function mount()
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $this->user = $user->load('student.studentattachmentfiles');
        $this->student = $this->user->student;

        // Inisialisasi semua properti dari data yang ada
        $this->name = $this->user->name;
        $this->no_tlp_hp = $this->user->no_tlp_hp;

        $this->nik = $this->student->nik;
        $this->tmpt_lhr = $this->student->tmpt_lhr;
        $this->tgl_lhr = $this->student->tgl_lhr;
        $this->kelamin = $this->student->kelamin;
        $this->kebangsaan = $this->student->kebangsaan;
        $this->no_tlp_rmh = $this->student->no_tlp_rmh;
        $this->no_tlp_kntr = $this->student->no_tlp_kntr;
        $this->kualifikasi_pendidikan = $this->student->kualifikasi_pendidikan;
        $this->nama_institusi = $this->student->nama_institusi;
        $this->jabatan = $this->student->jabatan;
        $this->alamat_kantor = $this->student->alamat_kantor;
        $this->no_tlp_email_fax = $this->student->no_tlp_email_fax;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            // Update data User
            $this->user->fill([
                'name' => $this->name,
                'no_tlp_hp' => $this->no_tlp_hp,
            ]);

            // Update data Student
            $this->student->fill([
                'nik' => $this->nik,
                'tmpt_lhr' => $this->tmpt_lhr,
                'tgl_lhr' => $this->tgl_lhr,
                'kelamin' => $this->kelamin,
                'kebangsaan' => $this->kebangsaan,
                'no_tlp_rmh' => $this->no_tlp_rmh,
                'no_tlp_kntr' => $this->no_tlp_kntr,
                'kualifikasi_pendidikan' => $this->kualifikasi_pendidikan,
                'nama_institusi' => $this->nama_institusi,
                'jabatan' => $this->jabatan,
                'alamat_kantor' => $this->alamat_kantor,
                'no_tlp_email_fax' => $this->no_tlp_email_fax,
            ]);

            // Handle file upload tunggal
            foreach (['foto_ktp', 'foto_ktm', 'pas_foto'] as $fileField) {
                if ($this->$fileField) {
                    if ($this->student->$fileField && Storage::disk('public')->exists($this->student->$fileField)) {
                        Storage::disk('public')->delete($this->student->$fileField);
                    }
                    $fileData = FileHelper::storeFileWithUniqueName($this->$fileField, $fileField);
                    $this->student->$fileField = $fileData['path'];
                }
            }

            // Handle multiple file upload (KHS)
            if (!empty($this->kartu_hasil_studi)) {
                $oldKhsFiles = Studentattachmentfile::where('student_id', $this->student->id)
                    ->where('type', 'kartu_hasil_studi')->get();

                foreach ($oldKhsFiles as $oldFile) {
                    Storage::disk('public')->delete($oldFile->path_file);
                    $oldFile->delete();
                }

                foreach ($this->kartu_hasil_studi as $file) {
                    $fileData = FileHelper::storeFileWithUniqueName($file, "student_attachments");
                    Studentattachmentfile::create([
                        'student_id' => $this->student->id,
                        'type' => 'kartu_hasil_studi',
                        'path_file' => $fileData['path'],
                    ]);
                }
            }

            if ($this->student->isDirty()) {
                $this->student->save();
            }
            if ($this->user->isDirty()) {
                $this->user->save();
            }
        });

        // Refresh data setelah disimpan
        $this->mount();
        $this->reset('foto_ktp', 'foto_ktm', 'pas_foto', 'kartu_hasil_studi');

        $this->dispatch('notify', message: 'Profil berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.asesi.profile.update-profile-asesi');
    }
}
