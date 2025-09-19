<?php

namespace App\Livewire\Admin;

use App\Models\Asesi;
use App\Models\Sertification;
use App\Notifications\RincianPembayaranUpdated;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Kreait\Firebase\Contract\Messaging; 
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification; 
#[Layout('layouts.admin')]
class Pembayaran extends Component
{
    // Properti untuk menyimpan data
    public Sertification $sertification;
    public int $sertificationId;
    public bool $editingRincian = false;

    // Properti yang akan diikat ke form (mirip state)
    public string $rincian_pembayaran = '';
    public ?string $tgl_bayar_ditutup = null;
    public ?string $harga = null;

    // Aturan validasi
    protected $rules = [
        'rincian_pembayaran' => 'required|string',
        'tgl_bayar_ditutup' => 'required|date',
        'harga' => 'required|numeric|min:0',
    ];

    // Fungsi ini berjalan sekali saat komponen dimuat (pengganti Controller::index)
    public function mount($sert_id)
    {
        $this->sertificationId = $sert_id;
        $this->sertification = Sertification::with('skema')->findOrFail($sert_id);

        // Atur state awal
        $this->editingRincian = !$this->sertification->punya_rincian_pembayaran;
        $this->rincian_pembayaran = $this->sertification->rincian_pembayaran ?? Sertification::RINCIAN_DEFAULT;
        $this->tgl_bayar_ditutup = $this->sertification->tgl_bayar_ditutup?->format('Y-m-d\TH:i');
        $this->harga = $this->sertification->harga;
    }

    // Fungsi ini berjalan saat tombol "Simpan" diklik (pengganti Controller::update)
    public function save(Messaging $messaging)
    {
        $this->validate();

        $this->sertification->fill([
            'rincian_pembayaran' => $this->rincian_pembayaran,
            'tgl_bayar_ditutup' => $this->tgl_bayar_ditutup,
            'harga' => $this->harga,
        ]);
        if (Auth::check()) {
            $this->sertification->rincianbayar_madeby = Auth::id();
        }
        if (is_null($this->sertification->rincianbayar_createdat)) {
            $this->sertification->rincianbayar_createdat = now();
        } else {
            $this->sertification->rincianbayar_updatedat = now();
        }
        $this->sertification->save();
        $asesis = Asesi::with('student.user')
            ->where('sertification_id', $this->sertificationId)
            ->where('status', 'dilanjutkan_asesmen')
            ->whereHas('student.user', function ($query) {
                $query->whereNotNull('fcm_token');
            })
            ->get();

        if ($asesis->isNotEmpty()) {
            // 2. Siapkan konten notifikasi
            $title = 'Rincian Pembayaran Diperbarui untuk sertifikasi'. $this->sertification->skema->nama_skema;
            $body = 'Silakan periksa rincian pembayaran terbaru untuk skema';

            // 3. Kirim pesan ke setiap asesi secara individual karena URL-nya unik
            foreach ($asesis as $asesi) {
                if ($user = $asesi->student->user) {
                    // Buat URL unik untuk setiap asesi
                    $url = route('asesi.applied.payment.create', [$this->sertification->id, $asesi->id]);
                    
                    // Buat pesan spesifik untuk user ini
                    $message = CloudMessage::new()
                        ->withNotification(FirebaseNotification::create($title, $body))
                        ->withData(['url' => $url]);

                    // Kirim pesan menggunakan try-catch untuk menangani error per pengguna
                    try {
                        $messaging->send($message->toToken($user->fcm_token));
                    } catch (\Throwable $e) {
                        // Log::error("Gagal mengirim notifikasi pembayaran ke user {$user->id}: " . $e->getMessage());
                    }
                }
            }
        }
        // Kirim notifikasi ke Asesi
        $this->notifyAsesi();

        // Kirim notifikasi sukses ke frontend
        $this->dispatch('notify', message: 'Rincian pembayaran berhasil disimpan!');
        $this->editingRincian = false; // Kembali ke mode tampilan
    }

    // Fungsi untuk mengirim notifikasi
    protected function notifyAsesi()
    {
        $asesis = Asesi::with(['student.user'])
            ->where('sertification_id', $this->sertificationId)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        foreach ($asesis as $asesi) {
            $user = $asesi->student->user ?? null;
            if ($user) {
                $user->notify(new RincianPembayaranUpdated($this->sertification, $asesi));
            }
        }
    }

    // Fungsi yang merender view
    public function render()
    {
        return view('livewire.admin.pembayaran');
    }
}
