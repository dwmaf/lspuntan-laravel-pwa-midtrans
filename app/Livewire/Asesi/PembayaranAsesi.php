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
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
#[Layout('layouts.app')]
class PembayaranAsesi extends Component
{
    use WithFileUploads;
    public Sertification $sertification;
    public Asesi $asesi;
    public $bukti_bayar;

    public function mount($sert_id, $asesi_id)
    {
        $this->sertification = Sertification::with('asesor', 'skema', 'pembuatrincianpembayaran.asesor')->find($sert_id);
        $this->asesi = Asesi::with('student')->find($asesi_id);
    }
    public function save(Messaging $messaging)
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
        
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AsesiUploadBuktiPembayaran($this->sertification->id, $this->asesi->id));
            // 2. Siapkan konten notifikasi
            $title = 'Pembayaran Baru Diterima';
            $body = 'Seorang asesi telah mengunggah bukti pembayaran. Silakan periksa.';
            $url = route('admin.sertifikasi.pendaftar.show', [$this->sertification->id, $this->asesi->id]);
            foreach ($admins as $admin) {
                if ($user = $admin->user) {
                    if ($user->fcm_token) {
                        $message = CloudMessage::new()
                            ->withNotification(FirebaseNotification::create($title, $body))->withData(['url' => $url]);

                        try {
                            $messaging->send($message->toToken($user->fcm_token));
                        } catch (NotFound $e) {
                            Log::warning("Token FCM tidak valid untuk user {$user->id}. Menghapus token.");
                            $user->update(['fcm_token' => null]);
                        } catch (\Throwable $e) {
                            Log::error("Gagal mengirim notifikasi asesi membayar sertfikasi ke user {$user->id}: " . $e->getMessage());
                        }
                    }
                } 
            }
        }
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
