<?php

namespace App\Livewire\Asesi;

use App\Models\Sertification;
use App\Models\Pengumumanasesmen;
use App\Models\Asesi;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Attributes\Layout;
#[Layout('layouts.app')]
class PengumumanAsesi extends Component
{
    public Sertification $sertification;
    public Asesi $asesi;
    public Collection $pengumumanasesmen;

    public function mount($sert_id, $asesi_id)
    {
        $this->sertification = Sertification::with('pengumumanasesmen.pembuatpengumuman.asesor')->findOrFail($sert_id);
        $this->asesi = Asesi::with('student')->findOrFail($asesi_id);
        $this->pengumumanasesmen = $this->sertification->pengumumanasesmen;
    }

    public function render()
    {
        return view('livewire.asesi.pengumuman-asesi');
    }
}
