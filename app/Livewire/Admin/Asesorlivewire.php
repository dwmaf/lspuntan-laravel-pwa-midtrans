<?php

namespace App\Livewire\Admin;

use App\Models\Asesor;
use App\Models\Skema;
use App\Models\User;
use App\Notifications\AsesorAccountCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Asesorlivewire extends Component
{
    // Properti untuk daftar
    public $asesors;
    public $allSkemas;

    // Properti untuk state & form
    public ?string $formMode = null; // 'create', 'edit', atau null
    public ?Asesor $editingAsesor = null;

    public string $name = '';
    public string $email = '';
    public ?string $no_tlp_hp = null;
    public string $password = '';
    public array $selectedSkemas = [];

    // Aturan validasi
    protected function rules()
    {
        $userId = $this->editingAsesor?->user_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email,' . $userId],
            'no_tlp_hp' => ['nullable', 'string', 'max:15'],
            'password' => ['nullable', 'string', 'min:8'],
            'selectedSkemas' => ['required', 'array', 'min:1'],
            'selectedSkemas.*' => ['exists:skemas,id'],
        ];
    }

    // Validasi real-time
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Inisialisasi komponen
    public function mount()
    {
        $this->loadAsesors();
        $this->allSkemas = Skema::all();
    }

    public function loadAsesors()
    {
        $this->asesors = Asesor::with('skemas', 'user')->withCount('sertifications')->latest()->get();
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->formMode = 'create';
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'no_tlp_hp' => $this->no_tlp_hp,
                'password' => Hash::make($this->password),
            ]);

            $user->assignRole('asesor');

            $asesor = Asesor::create(['user_id' => $user->id]);
            $asesor->skemas()->attach($this->selectedSkemas);

            $user->markEmailAsVerified();
            // ketika mau aktifin kirim emailnya, ini diuncomment
            // $user->notify(new AsesorAccountCreated());
        });

        $this->dispatch('notify', message: 'Asesor berhasil ditambah & notifikasi email terkirim!');
        $this->resetForm();
        $this->loadAsesors();
    }

    public function edit(int $asesorId)
    {
        $this->resetForm();
        $this->formMode = 'edit';
        $this->editingAsesor = Asesor::with('user', 'skemas')->findOrFail($asesorId);

        $this->name = $this->editingAsesor->user->name;
        $this->email = $this->editingAsesor->user->email;
        $this->no_tlp_hp = $this->editingAsesor->user->no_tlp_hp;
        $this->selectedSkemas = $this->editingAsesor->skemas->pluck('id')->toArray();
    }

    public function update()
    {
        if ($this->formMode !== 'edit') return;

        $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'no_tlp_hp' => $this->no_tlp_hp,
        ];

        // if (!empty($this->password)) {
        //     $userData['password'] = Hash::make($this->password);
        // }

        $this->editingAsesor->user->update($userData);
        $this->editingAsesor->skemas()->sync($this->selectedSkemas);

        $this->dispatch('notify', message: 'Data asesor berhasil diperbarui!');
        $this->resetForm();
        $this->loadAsesors();
    }

    public function destroy(int $asesorId)
    {
        $asesor = Asesor::with('user')->findOrFail($asesorId);
        DB::transaction(function () use ($asesor) {
            $asesor->skemas()->detach();
            $asesor->delete();
            if ($asesor->user) {
                $asesor->user->delete();
            }
        });

        $this->dispatch('notify', message: 'Asesor berhasil dihapus!');
        $this->loadAsesors();
    }

    public function resetForm()
    {
        $this->reset(['formMode', 'editingAsesor', 'name', 'email', 'no_tlp_hp', 'password', 'selectedSkemas']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.asesorlivewire');
    }
}
