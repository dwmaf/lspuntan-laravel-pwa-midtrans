<?php

namespace App\Livewire\Admin;

use App\Models\Skema;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.admin')]
class Skemalivewire extends Component
{
    use WithFileUploads;

    // Properti untuk daftar
    public $skemas;

    public ?string $formMode = null; // Bisa 'create', 'edit', atau null
    public ?Skema $editingSkema = null;

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

    // Fungsi ini berjalan setiap kali properti diperbarui (real-time validation)
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
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
        $this->editingSkema = new Skema(); // Inisialisasi model kosong
        $this->formMode = 'create';
    }

    public function save()
    {
        if (!$this->editingSkema) return; // Safety check

        $this->validate();

        $isCreating = !$this->editingSkema->exists;

        // Isi nama skema
        $this->editingSkema->nama_skema = $this->nama_skema;

        // Proses file APL 1 jika ada yang baru
        if ($this->format_apl_1) {
            // Jika ini adalah update, hapus file lama terlebih dahulu
            if (!$isCreating && $this->editingSkema->format_apl_1) {
                Storage::disk('public')->delete($this->editingSkema->format_apl_1);
            }
            $this->editingSkema->format_apl_1 = $this->format_apl_1->store('apl_files', 'public');
        }

        // Proses file APL 2 jika ada yang baru
        if ($this->format_apl_2) {
            // Jika ini adalah update, hapus file lama terlebih dahulu
            if (!$isCreating && $this->editingSkema->format_apl_2) {
                Storage::disk('public')->delete($this->editingSkema->format_apl_2);
            }
            $this->editingSkema->format_apl_2 = $this->format_apl_2->store('apl_files', 'public');
        }

        $this->editingSkema->save(); // Eloquent akan menangani create atau update secara otomatis

        $this->dispatch('notify', message: 'Skema berhasil ' . ($isCreating ? 'ditambahkan!' : 'diperbarui!'));
        $this->resetForm();
        $this->loadSkemas();
    }
    
    public function edit(int $skemaId)
    {
        $this->resetForm();
        $this->formMode = 'edit'; // Set mode ke 'edit'
        $this->editingSkema = Skema::findOrFail($skemaId);
        $this->nama_skema = $this->editingSkema->nama_skema;
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
        $this->reset(['formMode','editingSkema', 'nama_skema', 'format_apl_1', 'format_apl_2']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.skemalivewire');
    }
}