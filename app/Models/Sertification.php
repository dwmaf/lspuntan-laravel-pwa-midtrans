<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // <-- Tambahkan ini
use Carbon\Carbon; // <-- Tambahkan ini

class Sertification extends Model
{
    /** @use HasFactory<\Database\Factories\SertificationFactory> */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function asesor()
    {
        return $this->belongsTo(Asesor::class);
    }
    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }
    public function asesi()
    {
        return $this->hasMany(Asesi::class);
    }
    public function praasesmenfile()
    {
        return $this->hasMany(Praasesmenfile::class);
    }
    public function pengumumanasesmen()
    {
        return $this->hasMany(Pengumumanasesmen::class);
    }
    public function tugasasesmenattachmentfile()
    {
        return $this->hasMany(Tugasasesmenattachmentfile::class);
    }
    public function pembuatrincianpembayaran()
    {
        return $this->belongsTo(User::class, 'rincianbayar_madeby');
    }
    public function pembuatrinciantugasasesmen()
    {
        return $this->belongsTo(User::class, 'tugasasesmen_madeby');
    }


    public const RINCIAN_DEFAULT = 'Silahkan buat rincian pembayaran...';
    public const RINCIAN_DEFAULT_ASESMEN = 'Silahkan buat rincian tugas asesmen...';

    protected $casts = [
        'rincianbayar_createdat' => 'datetime',
        'tgl_bayar_ditutup' => 'datetime',
        'tugasasesmen_createdat' => 'datetime',
        'batas_pengumpulan_tugas_asesmen' => 'datetime',
    ];

    protected function punyaRincianPembayaran(): Attribute
    {
        return Attribute::make(
            get: fn() => !empty($this->rincian_pembayaran) && $this->rincian_pembayaran !== self::RINCIAN_DEFAULT,
        );
    }

    protected function tanggalRincianBayarDibuatFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->rincianbayar_createdat) {
                    return 'N/A';
                }
                if ($this->rincianbayar_createdat->isToday()) {
                    return $this->rincianbayar_createdat->format('H:i');
                }
                return $this->rincianbayar_createdat->format('d M Y');
            }
        );
    }

    protected function punyaRincianAsesmen(): Attribute
    {
        return Attribute::make(
            get: fn() => !empty($this->rincian_tugas_asesmen) && $this->rincian_tugas_asesmen !== self::RINCIAN_DEFAULT_ASESMEN,
        );
    }

    protected function tanggalRincianAsesmenDibuatFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->tugasasesmen_createdat) {
                    return 'N/A';
                }
                if ($this->tugasasesmen_createdat->isToday()) {
                    return $this->tugasasesmen_createdat->format('H:i');
                }
                return $this->tugasasesmen_createdat->format('d M Y');
            }
        );
    }

    protected function batasPengumpulanFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->batas_pengumpulan_tugas_asesmen) {
                    return 'Tidak ada batas pengumpulan';
                }
                return $this->batas_pengumpulan_tugas_asesmen->format('d M Y H:i');
            }
        );
    }
}
