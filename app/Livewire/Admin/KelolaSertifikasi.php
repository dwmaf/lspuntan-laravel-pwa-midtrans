<?php

namespace App\Livewire\Admin;

use App\Models\Asesor;
use App\Models\Sertification;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class KelolaSertifikasi extends Component
{
    // Properti untuk state & data
    public string $tab = 'mulai';
    public $sertifications_berlangsung;
    public $sertifications_selesai;
    public $asesors;

    // Properti untuk form "Mulai Sertifikasi"
    public string $asesor_skema = '';
    public ?string $tgl_apply_dibuka = null;
    public ?string $tgl_apply_ditutup = null;
    public ?string $tgl_bayar_ditutup = null;
    public ?string $harga = null;
    public ?string $tuk = null;

    // Properti untuk filter "Riwayat"
    public string $selectedFilter = 'semua';

    // Aturan validasi untuk form
    protected function rules()
    {
        return [
            'asesor_skema' => 'required',
            'tgl_apply_dibuka' => 'required|date',
            'tgl_apply_ditutup' => 'required|date|after_or_equal:tgl_apply_dibuka',
            'tgl_bayar_ditutup' => 'required|date',
            'harga' => 'required|numeric|min:0',
            'tuk' => 'required|string|max:255',
        ];
    }

    // Berjalan saat komponen pertama kali dimuat
    public function mount()
    {
        $this->loadData();
    }

    // Method untuk memuat semua data awal
    public function loadData()
    {
        $this->sertifications_berlangsung = Sertification::with('skema', 'asesor')
            ->where('status', 'berlangsung')
            ->orderBy('tgl_apply_dibuka', 'desc')
            ->get();

        $this->asesors = Asesor::with('skemas', 'user')->get();
        
        // Panggil method filter untuk memuat data riwayat awal
        $this->filterRiwayat();
    }

    // Method untuk menyimpan form "Mulai Sertifikasi"
    public function save()
    {
        $validatedData = $this->validate();
        
        list($asesor_id, $skema_id) = explode(',', $this->asesor_skema);
        
        Sertification::create([
            'asesor_id' => $asesor_id,
            'skema_id' => $skema_id,
            'tgl_apply_dibuka' => $this->tgl_apply_dibuka,
            'tgl_apply_ditutup' => $this->tgl_apply_ditutup,
            'tgl_bayar_ditutup' => $this->tgl_bayar_ditutup,
            'harga' => $this->harga,
            'tuk' => $this->tuk,
            'status' => 'berlangsung',
        ]);

        $this->resetForm();
        $this->loadData(); // Muat ulang data setelah menyimpan
        $this->tab = 'berlangsung'; // Pindah ke tab "Berlangsung"
        $this->dispatch('notify', message: 'Sertifikasi berhasil dimulai!');
    }

    // Method untuk memfilter riwayat, menggantikan fungsi AJAX
    public function filterRiwayat($filter = null)
    {
        // Jika filter baru dipilih, update properti
        if ($filter) {
            $this->selectedFilter = $filter;
        }

        $query = Sertification::with('skema', 'asesor')->where('status', 'selesai');

        switch ($this->selectedFilter) {
            case 'bulan_ini':
                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                break;
            case '3_bulan':
                $query->where('created_at', '>=', now()->subMonths(3));
                break;
            case 'tahun_ini':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $this->sertifications_selesai = $query->orderBy('tgl_apply_ditutup', 'desc')->get();
    }

    public function resetForm()
    {
        $this->reset(['asesor_skema', 'tgl_apply_dibuka', 'tgl_apply_ditutup', 'tgl_bayar_ditutup', 'harga', 'tuk']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.kelola-sertifikasi');
    }
}
