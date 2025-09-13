<?php

namespace App\Livewire\Asesi;


use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\Sertification;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use App\Models\Asesiasesmenfile;
use App\Notifications\AsesiUploadTugasAsesmen;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class AsesmenAsesi extends Component
{
    use WithFileUploads;
    public Sertification $sertification;
    public Asesi $asesi;
    public $asesiasesmenfiles = [];

    public function mount($sert_id, $asesi_id)
    {
        $this->sertification = Sertification::with('pembuatrinciantugasasesmen.asesor')->find($sert_id);
        $this->asesi = Asesi::with('asesiasesmenfiles')->find($asesi_id);   
    }

    public function save()
    {
        $this->validate([
            'asesiasesmenfiles' => 'required|array|max:5',
            'asesiasesmenfiles.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB
        ]);
        if ($this->asesiasesmenfiles) {
            // 1. Hapus file lama dari DB dan storage untuk tipe ini
            $oldFilePaths = Asesiasesmenfile::where('asesi_id', $this->asesi->id)
                ->pluck('path_file');
            if ($oldFilePaths->isNotEmpty()) {
                Asesiasesmenfile::where('asesi_id', $this->asesi->id)->delete();
                // 3. Hapus semua file fisik (1 Panggilan Storage)
                Storage::disk('public')->delete($oldFilePaths->all());
            }
            $newFilesData = [];
            $now = now();
            foreach ($this->asesiasesmenfiles as $file) {
                $newFilesData[] = [
                    'asesi_id' => $this->asesi->id,
                    'path_file' => FileHelper::storeFileWithUniqueName($file, 'asesi_files')['path'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (!empty($newFilesData)) {
                Asesiasesmenfile::insert($newFilesData);
            }
        }
        $this->notifyAdmin();
        
        $this->asesi->refresh(); // Muat ulang relasi asesiasesmenfiles
        $this->reset('asesiasesmenfiles'); // Kosongkan input file setelah berhasil
        $this->dispatch('notify', message: 'Berhasil unggah file asesmen.');
    }
    public function deleteFile($fileId)
    {
        $file = Asesiasesmenfile::find($fileId);
        if ($file && $file->asesi_id === $this->asesi->id) { // Keamanan: pastikan file milik asesi ini
            if (Storage::disk('public')->exists($file->path_file)) {
                Storage::disk('public')->delete($file->path_file);
            }
            $file->delete();
            $this->asesi->refresh(); // Muat ulang relasi untuk memperbarui tampilan
            $this->dispatch('notify', message: 'File berhasil dihapus.');
        }
    }
    protected function notifyAdmin()
    {
        $sertification = Sertification::with(['asesor.user'])
            ->find($this->sertification->id);
        $asesor = $sertification->asesor->user;
        $asesor->notify(new AsesiUploadTugasAsesmen($this->sertification->id, $this->asesi->id));
    }

    public function render()
    {
        return view('livewire.asesi.asesmen-asesi');
    }
}
