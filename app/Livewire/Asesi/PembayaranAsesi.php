<?php

namespace App\Livewire\Asesi;

use App\Models\Asesi;
use App\Models\Sertification;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AsesiUploadBuktiPembayaran;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
#[Layout('layouts.app')]
class PembayaranAsesi extends Component
{
    use WithFileUploads;
    public Sertification $sertification;
    public Asesi $asesi;
    public $bukti_bayar;
    
    public function mount($sert_id, $asesi_id)
    {
        $this->sertification = Sertification::with('asesor', 'skema','pembuatrincianpembayaran.asesor')->find($sert_id);
        $this->asesi = Asesi::with('student')->find($asesi_id);   
    }
    public function save()
    {
        $this->validate([
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ]);
        
        $transaction = Transaction::firstOrCreate(
            ['asesi_id' => $this->asesi->id, 'sertification_id' => $this->sertification->id],
            ['status' => 'pending', 'tipe' => 'manual']
        );
        if ($this->bukti_bayar) {
            if ($transaction->bukti_bayar && Storage::disk('public')->exists($transaction->bukti_bayar)) {
                Storage::disk('public')->delete($transaction->bukti_bayar);
            }
            $transaction->bukti_bayar = FileHelper::storeFileWithUniqueName($this->bukti_bayar, 'asesi_files')['path'];
        }
        $transaction['status'] = 'pending';
        $transaction->save();
        $admins = User::role('admin')->get();
        Notification::send($admins, new AsesiUploadBuktiPembayaran($this->sertification->id, $this->asesi->id));

        // PERBAIKAN 3: Beri feedback ke pengguna
        $this->asesi->refresh(); // Muat ulang data transaksi asesi
        $this->reset('bukti_bayar'); // Kosongkan input file
        $this->dispatch('notify', message: 'Berhasil unggah bukti pembayaran, admin akan memverifikasinya.');
    }
    public function render()
    {
        return view('livewire.asesi.pembayaran-asesi');
    }
}
