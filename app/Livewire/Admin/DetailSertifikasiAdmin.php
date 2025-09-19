<?php

namespace App\Livewire\Admin;


use App\Models\Asesor;
use App\Models\Sertification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class DetailSertifikasiAdmin extends Component
{
    public Sertification $sertification;
    

    // State
    public bool $isEditing = false;

    public string $asesor_skema = '';
    public ?string $tgl_apply_dibuka = null;
    public ?string $tgl_apply_ditutup = null;
    public ?string $tgl_bayar_ditutup = null;
    public ?int $harga = null;
    public ?string $tuk = null;
    public ?string $status = null;

    protected function rules()
    {

        return [
            'asesor_skema' => 'required',
            'tgl_apply_dibuka' => 'required|date',
            'tgl_apply_ditutup' => 'required|date|after_or_equal:tgl_apply_dibuka',
            'tgl_bayar_ditutup' => 'required|date',
            'harga' => 'required|numeric|min:0',
            'tuk' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ];
    }

    public function mount($sert_id)
    {
        $this->sertification = Sertification::with('skema','asesor')->findOrFail($sert_id);
        $this->asesor_skema = $this->sertification->asesor_id . ',' . $this->sertification->skema_id;
        $this->tgl_apply_dibuka = $this->sertification->tgl_apply_dibuka;
        $this->tgl_apply_ditutup = $this->sertification->tgl_apply_ditutup;
        $this->tgl_bayar_ditutup = $this->sertification->tgl_bayar_ditutup;
        $this->harga = $this->sertification->harga;
        $this->tuk = $this->sertification->tuk;
        $this->status = $this->sertification->status;
        
    } 

    public function enterEditMode()
    {
        $this->isEditing = true;
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->resetErrorBag();
        $this->mount($this->sertification->id);
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            // Logika penyimpanan dari controller (hanya untuk create)
            [$asesor_id, $skema_id] = explode(',', $this->asesor_skema);
            $this->sertification->update([
                'asesor_id' => $asesor_id,
                'skema_id' => $skema_id,
                'tgl_apply_dibuka' => $this->tgl_apply_dibuka,
                'tgl_apply_ditutup' => $this->tgl_apply_ditutup,
                'tgl_bayar_ditutup' => $this->tgl_bayar_ditutup,
                'harga' => $this->harga,
                'tuk' => $this->tuk,
                'status' => $this->status,
            ]);
            


        });

        $this->isEditing = false; // Kembali ke mode 'show'
        $this->dispatch('notify', message: 'Data berhasil diperbarui!');
    }

    public function render()
    {
        $asesors = Asesor::with('skemas')->get();
        return view('livewire.admin.detail-sertifikasi-admin', compact('asesors'));
    }
}
