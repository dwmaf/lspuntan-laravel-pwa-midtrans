<?php

namespace App\Livewire\Admin;

use App\Models\Asesi;
use App\Models\Pengumumanasesmen;
use App\Models\Pengumumanasesmenfile;
use App\Models\Sertification;
use App\Notifications\PengumumanBaru;
use App\Notifications\PengumumanUpdated;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

#[Layout('layouts.admin')]
class Pengumuman extends Component
{
    use WithFileUploads;

    // Properti utama
    public int $sertificationId;
    public $pengumumans;
    public Sertification $sertification;

    // Properti untuk form
    public ?string $formMode = null;
    public ?Pengumumanasesmen $editingPengumuman = null;
    public string $rincian_pengumuman_asesmen = '';
    public array $newFiles = [];
    public $existingFiles = [];

    protected function rules()
    {
        return [
            'rincian_pengumuman_asesmen' => 'required|string',
            'newFiles.*' => 'file|max:2048|mimes:jpg,jpeg,png,pdf,docx,pptx,xls,xlsx',
        ];
    }

    public function mount($sert_id)
    {
        $this->sertificationId = $sert_id;
        $this->sertification = Sertification::findOrFail($sert_id);
        $this->loadPengumumans();
    }

    public function loadPengumumans()
    {
        $this->pengumumans = Sertification::findOrFail($this->sertificationId)
            ->pengumumanasesmen()
            ->with('pembuatpengumuman.asesor.user', 'pengumumanasesmenfile')
            ->latest()
            ->get();
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->editingPengumuman = new Pengumumanasesmen(); // Mode create
        $this->formMode = 'create';
    }

    public function showEditForm(int $pengumumanId)
    {
        $this->resetForm();
        $this->editingPengumuman = Pengumumanasesmen::with('pengumumanasesmenfile')->findOrFail($pengumumanId);
        $this->rincian_pengumuman_asesmen = $this->editingPengumuman->rincian_pengumuman_asesmen;
        $this->existingFiles = collect($this->editingPengumuman->pengumumanasesmenfile);
        $this->formMode = 'edit';
    }

    public function save(Messaging $messaging)
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            // Jika validasi gagal, kirim event ke frontend untuk scroll
            $this->dispatch('validation-error');
            // Lempar kembali exception agar Livewire tetap menampilkan error
            throw $e;
        }

        $isCreating = !$this->editingPengumuman->exists;

        $this->editingPengumuman->fill([
            'sertification_id' => $this->sertificationId,
            'rincian_pengumuman_asesmen' => $this->rincian_pengumuman_asesmen,
            'pengumuman_madeby' => Auth::id(),
        ]);
        $this->editingPengumuman->save();

        // Simpan file baru
        foreach ($this->newFiles as $file) {
            $path = $file->store('pengumuman_asesmen_attachment_file', 'public');
            Pengumumanasesmenfile::create([
                'pengumumanasesmen_id' => $this->editingPengumuman->id,
                'path_file' => $path,
            ]);
        }
        $asesis = Asesi::with('student.user')
            ->where('sertification_id', $this->sertificationId)
            ->where('status', 'dilanjutkan_asesmen')
            ->whereHas('student.user', function ($query) {
                $query->whereNotNull('fcm_token');
            })
            ->get();

        if ($asesis->isNotEmpty()) {
            // 2. Siapkan konten notifikasi
            $title = $isCreating ? 'Ada Pengumuman Baru' : 'Pengumuman Diperbarui';
            $body = 'Pengumuman baru untuk sertifikasi: ' . $this->sertification->skema->nama_skema;
            $url = route('asesi.pengumuman.index', [$this->sertificationId]);
            // 3. Buat template pesan (tanpa data URL karena setiap asesi punya URL berbeda)
            $message = CloudMessage::new()
                ->withNotification(FirebaseNotification::create($title, $body))->withData(['url' => $url]);

            // 4. Kirim pesan ke setiap asesi secara individual karena URL-nya unik
            if (!empty($deviceTokens)) {
                $report = $messaging->sendMulticast($message, $deviceTokens);

                // (Opsional) Periksa jika ada kegagalan
                if ($report->hasFailures()) {
                    // Log::warning('Gagal mengirim beberapa notifikasi pengumuman.');
                    // Anda bisa menambahkan logging yang lebih detail di sini jika perlu
                }
            }
        }
        // Kirim notifikasi
        $this->notifyAsesi($isCreating);

        $this->dispatch('notify', message: 'Pengumuman berhasil ' . ($isCreating ? 'dibuat!' : 'diperbarui!'));
        if ($isCreating) {
            $this->dispatch('pengumuman-created');
        }
        $this->resetForm();
        $this->loadPengumumans();
    }

    public function deleteFile(int $fileId)
    {
        $file = Pengumumanasesmenfile::findOrFail($fileId);
        Storage::disk('public')->delete($file->path_file);
        $file->delete();

        // Refresh daftar file di form edit
        $this->showEditForm($this->editingPengumuman->id);
        $this->dispatch('notify', message: 'File lampiran dihapus!');
    }

    public function deletePengumuman(int $pengumumanId)
    {
        $pengumuman = Pengumumanasesmen::with('pengumumanasesmenfile')->findOrFail($pengumumanId);
        foreach ($pengumuman->pengumumanasesmenfile as $file) {
            Storage::disk('public')->delete($file->path_file);
        }
        $pengumuman->delete();

        $this->dispatch('notify', message: 'Pengumuman berhasil dihapus!');
        $this->loadPengumumans();
    }

    protected function notifyAsesi(bool $isCreating)
    {
        $asesis = Asesi::with('student.user')
            ->where('sertification_id', $this->sertificationId)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        $notificationClass = $isCreating ? PengumumanBaru::class : PengumumanUpdated::class;

        foreach ($asesis as $asesi) {
            if ($user = $asesi->student->user) {
                $user->notify(new $notificationClass($this->sertificationId, $asesi->id));
            }
        }
    }

    public function resetForm()
    {
        $this->reset(['formMode', 'editingPengumuman', 'rincian_pengumuman_asesmen', 'newFiles']);
        $this->existingFiles = collect();
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.pengumuman');
    }
}
