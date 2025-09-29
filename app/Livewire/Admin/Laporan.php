<?php

namespace App\Livewire\Admin;

use App\Models\Sertification;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Laporan extends Component
{
    public Sertification $sertification;
    public function mount($sert_id)
    {
        $this->sertification = Sertification::with('skema', 'asesor')->findOrFail($sert_id);
    }
    public function render()
    {
        return view('livewire.admin.laporan');
    }
}
