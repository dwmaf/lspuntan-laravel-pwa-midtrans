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
#[Layout('layouts.admin')]
class Pendaftar extends Component
{
    use WithFileUploads;

    public Sertification $sertification;
    public ?Asesi $selectedAsesi = null;

    // --- State untuk Modal dan Form ---
    public bool $showStatusAsesiModal = false;
    public ?string $newStatusAsesi = null;
    public ?string $catatanStatus = null;

    public bool $showStatusPembayaranModal = false;
    public ?string $newStatusPembayaran = null;
    public ?Transaction $transactionToUpdate = null;

    public bool $isEditingCertificate = false;
    public ?string $nomor_seri = null;
    public ?string $nomor_sertifikat = null;
    public ?string $nomor_registrasi = null;
    public ?string $tanggal_terbit = null;
    public ?string $berlaku_hingga = null;
    public $sertifikat_asesi;

    public function mount($sert_id)
    {
        $this->sertification = Sertification::with(['skema', 'asesi.student.user', 'asesi.transaction'])->findOrFail($sert_id);
    }

    // --- Aksi Navigasi ---
    public function selectAsesi($asesiId)
    {
        $this->selectedAsesi = Asesi::with([
            'student.user',
            'student.studentattachmentfile',
            'asesiattachmentfiles',
            'makulnilais',
            'transaction',
            'sertifikat'
        ])->findOrFail($asesiId);
        $this->isEditingCertificate = false; // Pastikan kembali ke mode lihat
    }

    public function backToList()
    {
        $this->selectedAsesi = null;
        // Refresh data daftar untuk melihat perubahan status
        $this->sertification->refresh();
    }

    // --- Aksi Status Asesi ---
    public function openStatusAsesiModal()
    {
        $this->reset('newStatusAsesi', 'catatanStatus');
        $this->showStatusAsesiModal = true;
    }

    public function updateStatusAsesi(Messaging $messaging)
    {
        $this->validate(['newStatusAsesi' => 'required|string']);

        $this->selectedAsesi->status = $this->newStatusAsesi;
        $this->selectedAsesi->save();

        $user = $this->selectedAsesi->student->user;

        if ($user->isNotEmpty()) {
            // 2. Siapkan konten notifikasi
            $title = 'Status asesmen Anda diubah menjadi: ' . $this->selectedAsesi->status;
            $url = route('asesi.sertifikasi.applied.show', [$this->sertification->id, $this->selectedAsesi->id]);
            // 3. Kirim pesan ke setiap asesi secara individual karena URL-nya unik
            $message = CloudMessage::new()
                ->withNotification(FirebaseNotification::create($title))
                ->withData(['url' => $url]);

            // Kirim pesan menggunakan try-catch untuk menangani error per pengguna
            try {
                $messaging->send($message->toToken($user->fcm_token));
            } catch (\Throwable $e) {
                // Log::error("Gagal mengirim notifikasi pembayaran ke user {$user->id}: " . $e->getMessage());
            }
        }
        $user->notify(new StatusAsesiUpdated($this->sertification->id, $this->selectedAsesi->id, $this->selectedAsesi->status));

        $this->showStatusAsesiModal = false;
        $this->dispatch('notify', message: 'Status asesi berhasil diperbarui!');
    }

    // --- Aksi Status Pembayaran ---
    public function openStatusPembayaranModal(int $transactionId)
    {
        $this->transactionToUpdate = Transaction::find($transactionId);
        $this->reset('newStatusPembayaran');
        $this->showStatusPembayaranModal = true;
    }

    public function updateStatusPembayaran(Messaging $messaging)
    {
        $this->validate(['newStatusPembayaran' => 'required|string']);

        if ($this->transactionToUpdate) {
            $this->transactionToUpdate->status = $this->newStatusPembayaran;
            $this->transactionToUpdate->save();

            $user = $this->transactionToUpdate->asesi->student->user;
            $user->notify(new StatusBayarAsesiUpdated($this->sertification->id, $this->selectedAsesi->id, $this->transactionToUpdate->status));
            if ($user->isNotEmpty()) {
                // 2. Siapkan konten notifikasi
                $title = 'Status pembayaran Anda diubah menjadi: ' . $this->selectedAsesi->status;
                $url = route('asesi.sertifikasi.applied.show', [$this->sertification->id, $this->selectedAsesi->id]);
                // 3. Kirim pesan ke setiap asesi secara individual karena URL-nya unik
                $message = CloudMessage::new()
                    ->withNotification(FirebaseNotification::create($title))
                    ->withData(['url' => $url]);

                // Kirim pesan menggunakan try-catch untuk menangani error per pengguna
                try {
                    $messaging->send($message->toToken($user->fcm_token));
                } catch (\Throwable $e) {
                    // Log::error("Gagal mengirim notifikasi pembayaran ke user {$user->id}: " . $e->getMessage());
                }
            }
            $this->selectedAsesi->refresh();
            $this->showStatusPembayaranModal = false;
            $this->dispatch('notify', message: 'Status pembayaran berhasil diperbarui!');
        }
    }

    // --- Aksi Sertifikat ---
    public function enterCertificateEditMode()
    {
        if ($this->selectedAsesi->sertifikat) {
            $sertifikat = $this->selectedAsesi->sertifikat;
            $this->nomor_seri = $sertifikat->nomor_seri;
            $this->nomor_sertifikat = $sertifikat->nomor_sertifikat;
            $this->nomor_registrasi = $sertifikat->nomor_registrasi;
            $this->tanggal_terbit = $sertifikat->tanggal_terbit;
            $this->berlaku_hingga = $sertifikat->berlaku_hingga;
        } else {
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
            'sertifikat_asesi' => ($this->selectedAsesi->sertifikat ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
        ];
        $this->validate($rules);

        $filePath = $this->selectedAsesi->sertifikat?->file_path;
        if ($this->sertifikat_asesi) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = FileHelper::storeFileWithUniqueName($this->sertifikat_asesi, 'sertifikat_asesi')['path'];
        }

        $this->selectedAsesi->sertifikat()->updateOrCreate(
            ['asesi_id' => $this->selectedAsesi->id],
            [
                'nomor_seri' => $this->nomor_seri,
                'nomor_sertifikat' => $this->nomor_sertifikat,
                'nomor_registrasi' => $this->nomor_registrasi,
                'tanggal_terbit' => $this->tanggal_terbit,
                'berlaku_hingga' => $this->berlaku_hingga,
                'file_path' => $filePath,
            ]
        );

        $user = $this->selectedAsesi->student->user;
        if ($user->isNotEmpty()) {
            // 2. Siapkan konten notifikasi
            $title = 'Sertifikat Anda telah diunggah.';
            $url = route('asesi.sertifikasi.applied.show', [$this->sertification->id, $this->selectedAsesi->id]);
            // 3. Kirim pesan ke setiap asesi secara individual karena URL-nya unik
            $message = CloudMessage::new()
                ->withNotification(FirebaseNotification::create($title))
                ->withData(['url' => $url]);

            // Kirim pesan menggunakan try-catch untuk menangani error per pengguna
            try {
                $messaging->send($message->toToken($user->fcm_token));
            } catch (\Throwable $e) {
                // Log::error("Gagal mengirim notifikasi pembayaran ke user {$user->id}: " . $e->getMessage());
            }
        }
        $user->notify(new SertifikatDiunggah($this->sertification->id, $this->selectedAsesi->id));

        $this->isEditingCertificate = false;
        $this->selectedAsesi->refresh();
        $this->dispatch('notify', message: 'Sertifikat berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.admin.pendaftar');
    }
}
