<?php

namespace App\Livewire\Admin;

use App\Models\Asesi;
use App\Models\Sertification;
use App\Notifications\RincianPembayaranUpdated;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

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
    public function mount(int $sertificationId)
    {
        $this->sertificationId = $sertificationId;
        $this->sertification = Sertification::findOrFail($sertificationId);

        // Atur state awal
        $this->editingRincian = !$this->sertification->punya_rincian_pembayaran;
        $this->rincian_pembayaran = $this->sertification->rincian_pembayaran ?? Sertification::RINCIAN_DEFAULT;
        $this->tgl_bayar_ditutup = $this->sertification->tgl_bayar_ditutup?->format('Y-m-d\TH:i');
        $this->harga = $this->sertification->harga;
    }

    // Fungsi ini berjalan saat tombol "Simpan" diklik (pengganti Controller::update)
    public function save()
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
