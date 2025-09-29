<?php

namespace App\Livewire\Admin;

use App\Models\Asesi;
use App\Models\Transaction;
use App\Notifications\SertifikatDiunggah;
use App\Notifications\StatusAsesiUpdated;
use App\Notifications\StatusBayarAsesiUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;
use App\Models\Sertification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use App\Http\Controllers\NotificationController;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class PendaftarDetail extends Component
{
    use WithFileUploads;

    public Asesi $asesi;
    public Sertification $sertification;
    public int $sertificationId;

    // --- State untuk Modal dan Form ---
    public bool $showConfirmEditStatusModal = false;
    public ?string $newStatusAsesi = null;
    public ?string $catatanStatus = null;

    public bool $showConfirmUpdateStatusPembayaranModal = false;
    public ?string $newStatusPembayaran = null;
    public ?Transaction $transactionToUpdate = null;

    public bool $isEditingCertificate = false;
    public ?string $nomor_seri = null;
    public ?string $nomor_sertifikat = null;
    public ?string $nomor_registrasi = null;
    public ?string $tanggal_terbit = null;
    public ?string $berlaku_hingga = null;
    public $sertifikat_asesi;

    public function mount(int $sert_id, int $asesi_id, Request $request)
    {
        NotificationController::markAsRead($request);
        $this->sertificationId = $sert_id;
        // Eager loading untuk semua detail yang dibutuhkan
        $this->sertification = Sertification::findOrFail($sert_id);
        $this->asesi = Asesi::with([
            'student.user',
            'student.studentattachmentfiles',
            'asesiattachmentfiles',
            'makulnilais',
            'transaction' => fn($q) => $q->latest(),
            'sertifikat'
        ])->findOrFail($asesi_id);
        $this->newStatusAsesi = $this->asesi->status;
        $latestTransaction = $this->asesi->transaction->first();
        $this->newStatusPembayaran = $latestTransaction?->status;
        $this->transactionToUpdate = $latestTransaction;
        if ($this->asesi->sertifikat) {
            $sertifikat = $this->asesi->sertifikat;
            $this->nomor_seri = $sertifikat->nomor_seri;
            $this->nomor_sertifikat = $sertifikat->nomor_sertifikat;
            $this->nomor_registrasi = $sertifikat->nomor_registrasi;
            $this->tanggal_terbit = $sertifikat->tanggal_terbit;
            $this->berlaku_hingga = $sertifikat->berlaku_hingga;
        }
    }

    public function updateStatusAsesi(Messaging $messaging)
    {
        $this->validate(['newStatusAsesi' => 'required|string']);

        $this->asesi->status = $this->newStatusAsesi;
        $this->asesi->save();

        $user = $this->asesi->student->user;

        if ($user) {
            Notification::send($user, new StatusAsesiUpdated($this->sertification->id, $this->asesi->id, $this->asesi->status));
            $title = 'Status asesmen Anda diubah menjadi: ' . $this->asesi->status;
            $url = route('asesi.sertifikasi.applied.show', [$this->sertification->id, $this->asesi->id]);
            if ($user->fcm_token) {
                $message = CloudMessage::new()
                    ->withNotification(FirebaseNotification::create($title))
                    ->withData(['url' => $url]);
                try {
                    $messaging->send($message->toToken($user->fcm_token));
                } catch (NotFound $e) {
                    Log::warning("Token FCM tidak valid untuk user {$user->id}. Menghapus token.");
                    $user->update(['fcm_token' => null]);
                } catch (\Throwable $e) {
                    Log::error("Gagal mengirim notifikasi status asesmen ke user {$user->id}: " . $e->getMessage());
                }
            }
            
        }
        $this->reset('catatanStatus');
        $this->showConfirmEditStatusModal = false;
        $this->dispatch('notify', message: 'Status asesi berhasil diperbarui!');
    }
    
    public function updateStatusPembayaran(Messaging $messaging)
    {
        $this->validate(['newStatusPembayaran' => 'required|string']);

        if ($this->transactionToUpdate) {
            $this->transactionToUpdate->status = $this->newStatusPembayaran;
            $this->transactionToUpdate->save();

            $user = $this->transactionToUpdate->asesi->student->user;
            if ($user) {
                // 2. Siapkan konten notifikasi
                $title = 'Status pembayaran Anda diubah menjadi: ' . $this->asesi->status;
                $url = route('asesi.sertifikasi.applied.show', [$this->sertification->id, $this->asesi->id]);
                // 3. Kirim pesan ke setiap asesi secara individual karena URL-nya unik
                if ($user->fcm_token) {
                    $message = CloudMessage::new()
                        ->withNotification(FirebaseNotification::create($title))
                        ->withData(['url' => $url]);

                    // Kirim pesan menggunakan try-catch untuk menangani error per pengguna
                    try {
                        $messaging->send($message->toToken($user->fcm_token));
                    } catch (NotFound $e) {
                        Log::warning("Token FCM tidak valid untuk user {$user->id}. Menghapus token.");
                        $user->update(['fcm_token' => null]);
                    } catch (\Throwable $e) {
                        Log::error("Gagal mengirim notifikasi tugas asesmen ke user {$user->id}: " . $e->getMessage());
                    }
                }
                Notification::send($user, new StatusBayarAsesiUpdated($this->sertification->id, $this->asesi->id, $this->transactionToUpdate->status));
            }
            $this->asesi->refresh();
            $this->showConfirmUpdateStatusPembayaranModal = false;
            $this->dispatch('notify', message: 'Status pembayaran berhasil diperbarui!');
        }
    }

    // --- Aksi Sertifikat ---
    public function enterCertificateEditMode()
    {
        $resetData = $this->asesi->sertifikat;
        if (!$resetData) {
            $this->reset('nomor_seri', 'nomor_sertifikat', 'nomor_registrasi', 'tanggal_terbit', 'berlaku_hingga');
        }
        $this->isEditingCertificate = true;
    }

    public function saveCertificate(Messaging $messaging)
    {
        $rules = [
            'nomor_seri' => 'nullable|string|max:255',
            'nomor_sertifikat' => 'nullable|string|max:255',
            'nomor_registrasi' => 'nullable|string|max:255',
            'tanggal_terbit' => 'required|date',
            'berlaku_hingga' => 'required|date|after_or_equal:tanggal_terbit',
            'sertifikat_asesi' => ($this->asesi->sertifikat ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
        ];
        $this->validate($rules);

        $filePath = $this->asesi->sertifikat?->file_path;
        if ($this->sertifikat_asesi) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = FileHelper::storeFileWithUniqueName($this->sertifikat_asesi, 'sertifikat_asesi')['path'];
        }

        $this->asesi->sertifikat()->updateOrCreate(
            ['asesi_id' => $this->asesi->id],
            [
                'nomor_seri' => $this->nomor_seri,
                'nomor_sertifikat' => $this->nomor_sertifikat,
                'nomor_registrasi' => $this->nomor_registrasi,
                'tanggal_terbit' => $this->tanggal_terbit,
                'berlaku_hingga' => $this->berlaku_hingga,
                'file_path' => $filePath,
            ]
        );

        $user = $this->asesi->student->user;
        if ($user) {
            // 2. Siapkan konten notifikasi
            $title = 'Sertifikat Anda telah diunggah.';
            $url = route('asesi.sertifikasi.applied.show', [$this->sertification->id, $this->asesi->id]);
            // 3. Kirim pesan ke setiap asesi secara individual karena URL-nya unik
            if ($user->fcm_token) {
                $message = CloudMessage::new()
                    ->withNotification(FirebaseNotification::create($title))
                    ->withData(['url' => $url]);

                // Kirim pesan menggunakan try-catch untuk menangani error per pengguna
                try {
                    $messaging->send($message->toToken($user->fcm_token));
                } catch (NotFound $e) {
                    Log::warning("Token FCM tidak valid untuk user {$user->id}. Menghapus token.");
                    $user->update(['fcm_token' => null]);
                } catch (\Throwable $e) {
                    Log::error("Gagal mengirim notifikasi sertifikat diunggah ke user {$user->id}: " . $e->getMessage());
                }
            }
            Notification::send($user, new SertifikatDiunggah($this->sertification->id, $this->asesi->id));
        }

        $this->isEditingCertificate = false;
        $this->asesi->refresh();
        $this->dispatch('notify', message: 'Sertifikat berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.admin.pendaftar-detail');
    }
}