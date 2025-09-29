<?php

namespace App\Livewire\Admin;

use App\Models\Skema;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;

#[Layout('layouts.admin')]
class Skemalivewire extends Component
{
    use WithFileUploads;

    // Properti untuk daftar
    public $skemas;

    public ?string $formMode = null; // Bisa 'create', 'edit', atau null
    public ?Skema $skema = null;

    public string $nama_skema = '';
    public $format_apl_1;
    public $format_apl_2;

    // Aturan validasi
    protected function rules()
    {
        return [
            'nama_skema' => ['required','string','max:255'],
            'format_apl_1' => ['nullable', 'file', 'mimes:docx,pdf', 'max:1024'],
            'format_apl_2' => ['nullable', 'file', 'mimes:docx,pdf', 'max:1024'],
        ];
    }

    // Berjalan saat komponen pertama kali dimuat
    public function mount()
    {
        $this->loadSkemas();
    }

    public function loadSkemas()
    {
        $this->skemas = Skema::latest()->get();
    }
    
    public function showCreateForm()
    {
        $this->resetForm();
        $this->skema = new Skema();
        $this->formMode = 'create';
    }

    public function save()
    {
        // if (!$this->skema) return; // Safety check

        $this->validate();
        $this->skema->nama_skema = $this->nama_skema;
        if ($this->format_apl_1) {
            if ($this->formMode === 'edit' && $this->skema->format_apl_1) {
                Storage::disk('public')->delete($this->skema->format_apl_1);
            }
            $this->skema->format_apl_1 = FileHelper::storeFileWithUniqueName($this->format_apl_1, 'apl_files')['path'];
        }

        if ($this->format_apl_2) {
            if ($this->formMode === 'edit' && $this->skema->format_apl_2) {
                Storage::disk('public')->delete($this->skema->format_apl_2);
            }
            $this->skema->format_apl_2 = FileHelper::storeFileWithUniqueName($this->format_apl_2, 'apl_files')['path'];
        }

        $this->skema->save();

        $this->dispatch('notify', message: 'Skema berhasil ' . ($this->formMode === 'create' ? 'ditambahkan!' : 'diperbarui!'));
        $this->resetForm();
        $this->loadSkemas();
    }
    
    public function showEditForm($skemaId)
    {
        $this->resetForm();
        $this->formMode = 'edit';
        $this->skema = Skema::findOrFail($skemaId);
        $this->nama_skema = $this->skema->nama_skema;
    }

    public function destroy(int $skemaId)
    {
        $skema = Skema::findOrFail($skemaId);
        foreach (['format_apl_1', 'format_apl_2'] as $fileField) {
            if ($skema->$fileField && Storage::disk('public')->exists($skema->$fileField)) {
                Storage::disk('public')->delete($skema->$fileField);
            }
        }
        $skema->delete();

        $this->dispatch('notify', message: 'Skema berhasil dihapus!');
        $this->loadSkemas();
    }

    public function resetForm()
    {
        $this->reset(['formMode','skema', 'nama_skema', 'format_apl_1', 'format_apl_2']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.skemalivewire');
    }
}