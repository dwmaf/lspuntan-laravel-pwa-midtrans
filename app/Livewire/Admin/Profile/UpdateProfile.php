<?php

namespace App\Livewire\Admin\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UpdateProfile extends Component
{
    public string $name = '';
    public ?string $no_tlp_hp = '';

    // Aturan validasi
    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'no_tlp_hp' => ['required', 'string', 'max:15'],
        ];
    }

    // Berjalan saat komponen pertama kali dimuat
    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->no_tlp_hp = $user->no_tlp_hp;
    }

    // Validasi real-time
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $validated = $this->validate();
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $user->fill($validated);
        $user->save();

        // Kirim event untuk notifikasi
        $this->dispatch('profile-updated', message: 'Informasi profil berhasil disimpan.');
    }
    public function render()
    {
        return view('livewire.admin.profile.update-profile');
    }
}
