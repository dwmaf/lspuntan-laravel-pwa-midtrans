<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Models\Tugasasesmenattachmentfile;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TugasAsesmenBaru;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification; // <-- IMPORT INI
#[Layout('layouts.admin')]
class Asesmen extends Component
{
    use WithFileUploads;

    public int $sertificationId;
    public bool $editingRincian = false;

    public string $rincian_tugas_asesmen = '';
    public ?string $batas_pengumpulan_tugas_asesmen = null;

    public $existingFiles;
    public array $newFiles = [];

    public int $maxFiles = 5;

    protected $rules = [
        'rincian_tugas_asesmen' => 'required|string',
        'batas_pengumpulan_tugas_asesmen' => 'nullable|date',
        'newFiles.*' => 'file|max:2048|mimes:jpg,jpeg,png,pdf,docx,pptx,xls,xlsx',
        'newFiles' => 'max:5',
    ];

    public function mount($sert_id)
    {
        $this->sertificationId = $sert_id;
        $sert = Sertification::with('tugasasesmenattachmentfile')->findOrFail($sert_id);

        $this->editingRincian = ! $sert->punya_rincian_asesmen;
        $this->rincian_tugas_asesmen = $sert->rincian_tugas_asesmen
            ?? Sertification::RINCIAN_DEFAULT_ASESMEN;
        $this->batas_pengumpulan_tugas_asesmen = $sert->batas_pengumpulan_tugas_asesmen;

        $this->existingFiles = $sert->tugasasesmenattachmentfile;
    }

    public function updatedNewFiles()
    {
        $this->resetErrorBag('newFiles');
        $this->resetErrorBag('newFiles.*');
        $this->validateOnly('newFiles.*');
        $this->validateOnly('newFiles');
        if ($this->totalCount() > $this->maxFiles) {
            $this->addError('newFiles', 'Maksimal total 5 file.');
        }
    }

    public function deleteFile(int $id)
    {
        $file = Tugasasesmenattachmentfile::where('sertification_id', $this->sertificationId)->find($id);
        if (!$file) return;

        if (Storage::disk('public')->exists($file->path_file)) {
            Storage::disk('public')->delete($file->path_file);
        }
        $file->delete();

        $this->dispatch('notify', message: 'File dihapus.');
        $this->refreshFilesOnly();
    }

    public function save(Messaging $messaging)
    {
        $this->validate();

        if ($this->totalCount() > $this->maxFiles) {
            $this->addError('newFiles', 'Maksimal total 5 file.');
            return;
        }

        $sert = Sertification::findOrFail($this->sertificationId);
        $sert->rincian_tugas_asesmen = $this->rincian_tugas_asesmen;
        $sert->batas_pengumpulan_tugas_asesmen = $this->batas_pengumpulan_tugas_asesmen;
        if (Auth::check()) {
            $sert->tugasasesmen_madeby = Auth::id();
        }
        if (is_null($sert->tugasasesmen_createdat)) {
            $sert->tugasasesmen_createdat = now();
        } else {
            $sert->tugasasesmen_updatedat = now();
        }
        $sert->save();

        if ($this->newFiles) {
            foreach ($this->newFiles as $file) {
                $path = FileHelper::storeFileWithUniqueName($file, 'asesmen_attachment_file');
                Tugasasesmenattachmentfile::create([
                    'sertification_id' => $this->sertificationId,
                    'path_file' => $path['path'],
                ]);
            }
        }
        $asesis = Asesi::with('student.user')
            ->where('sertification_id', $this->sert->id)
            ->where('status', 'dilanjutkan_asesmen')
            ->whereHas('student.user', function ($query) {
                $query->whereNotNull('fcm_token');
            })
            ->get();


        if ($asesis->isNotEmpty()) {
            // 2. Siapkan konten notifikasi
            $title = 'Instruksi Tugas asesmen diperbaharui.';
            $body = 'Silakan periksa instruksi tugas asesmen terbaru untuk Sertifikasi: ' . $sert->skema->nama_skema;
            
            foreach ($asesis as $asesi) {
                if ($user = $asesi->student->user) {
                    $url = route('asesi.assessmen.index', ['sert_id' => $this->sert->id, $asesi->id]);
                    $message = CloudMessage::new()
                        ->withNotification(FirebaseNotification::create($title, $body))
                        ->withData(['url' => $url]);

                    try {
                        $messaging->send($message->toToken($user->fcm_token));
                    } catch (\Throwable $e) {
                        // Log::error("Gagal mengirim notifikasi tugas asesmen ke user {$user->id}: " . $e->getMessage());
                    }
                }
            }
            // 3. Buat template pesan

        }
        $this->notifyAsesi($sert->id);
        $this->reset('newFiles');
        $this->refreshFilesOnly();

        $this->dispatch('notify', message: 'Rincian asesmen tersimpan.');
        $this->editingRincian = false;
    }

    protected function notifyAsesi($sertId)
    {
        $asesis = Asesi::with('student.user')
            ->where('sertification_id', $sertId)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        foreach ($asesis as $asesi) {
            $user = $asesi->student->user ?? null;
            if ($user) {
                Notification::send($user, new TugasAsesmenBaru($sertId, $asesi->id));
            }
        }
    }



    protected function refreshFilesOnly()
    {
        $sert = Sertification::with('tugasasesmenattachmentfile')->find($this->sertificationId);
        $this->existingFiles = $sert->tugasasesmenattachmentfile;
    }

    protected function totalCount(): int
    {
        return count($this->existingFiles) + count($this->newFiles);
    }

    public function render()
    {
        $sert = Sertification::find($this->sertificationId);
        return view('livewire.admin.asesmen', [
            'sert' => $sert,
            'existingCount' => count($this->existingFiles),
        ]);
    }
}
