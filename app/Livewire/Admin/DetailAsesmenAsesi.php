<?php

namespace App\Livewire\Admin;

use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\Sertification;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class DetailAsesmenAsesi extends Component
{
    public Sertification $sertification;
    public Asesi $asesi;
    public function mount($sert_id, $asesi_id, Request $request)
    {
        NotificationController::markAsRead($request);
        $this->asesi = Asesi::with('asesiasesmenfiles')->find($asesi_id);
        $this->sertification = Sertification::find($sert_id);
    }

    public function render()
    {
        return view('livewire.admin.detail-asesmen-asesi');
    }
}
