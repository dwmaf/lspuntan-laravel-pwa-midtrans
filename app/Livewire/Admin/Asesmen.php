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

class Asesmen extends Component
{
    use WithFileUploads;

    public int $sertificationId;
    public bool $editingRincian = false;

    public string $rincian_tugas_asesmen = '';
    public ?string $batas_pengumpulan_tugas_asesmen = null;

    public array $existingFiles = [];
    public array $newFiles = [];

    public int $maxFiles = 5;

    protected $rules = [
        'rincian_tugas_asesmen' => 'required|string',
        'batas_pengumpulan_tugas_asesmen' => 'nullable|date',
        'newFiles.*' => 'file|max:2048|mimes:jpg,jpeg,png,pdf,docx,pptx,xls,xlsx'
    ];

    public function mount(int $sertificationId)
    {
        $this->sertificationId = $sertificationId;
        $sert = Sertification::with('tugasasesmenattachmentfile')->findOrFail($sertificationId);

        $this->editingRincian = ! $sert->punya_rincian_asesmen;
        $this->rincian_tugas_asesmen = $sert->rincian_tugas_asesmen
            ?? Sertification::RINCIAN_DEFAULT_ASESMEN;
        $this->batas_pengumpulan_tugas_asesmen = $sert->batas_pengumpulan_tugas_asesmen;

        $this->mapExistingFiles($sert);
    }

    public function updatedNewFiles()
    {
        $this->validateOnly('newFiles.*');

        if ($this->totalCount() > $this->maxFiles) {
            $this->reset('newFiles');
            $this->addError('newFiles', 'Maksimal total 5 file (lama + baru).');
        }
    }

    public function removeNewFileTemp($index)
    {
        unset($this->newFiles[$index]);
        $this->newFiles = array_values($this->newFiles);
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

    public function save()
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

    protected function mapExistingFiles(Sertification $sert)
    {
        $this->existingFiles = $sert->tugasasesmenattachmentfile
            ->map(fn($f) => [
                'id' => $f->id,
                'name' => basename($f->path_file),
                'short' => strlen(basename($f->path_file)) > 24
                    ? substr(basename($f->path_file), 0, 24) . '...'
                    : basename($f->path_file),
                'url' => asset('storage/' . $f->path_file),
            ])->toArray();
    }

    protected function refreshFilesOnly()
    {
        $sert = Sertification::with('tugasasesmenattachmentfile')->find($this->sertificationId);
        $this->mapExistingFiles($sert);
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
