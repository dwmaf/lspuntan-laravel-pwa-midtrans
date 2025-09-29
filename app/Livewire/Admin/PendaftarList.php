<?php

namespace App\Livewire\Admin;

use App\Helpers\FileHelper;
use App\Models\Asesi;
use App\Models\Sertification;
use App\Models\Transaction;
use App\Notifications\SertifikatDiunggah;
use App\Notifications\StatusAsesiUpdated;
use App\Notifications\StatusBayarAsesiUpdated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;

#[Layout('layouts.admin')]
class PendaftarList extends Component
{
    use WithFileUploads;

    public Sertification $sertification;
    

    public function mount($sert_id)
    {
        $this->sertification = Sertification::with(['skema', 'asesi.student.user', 'asesi.transaction'])->findOrFail($sert_id);
    }


    public function render()
    {
        return view('livewire.admin.pendaftar-list');
    }
}
