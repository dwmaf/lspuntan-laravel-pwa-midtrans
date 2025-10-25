<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // <-- Tambahkan ini
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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
    public function news()
    {
        return $this->hasMany(News::class);
    }
    public function asesmenfiles()
    {
        return $this->hasMany(Asesmenfile::class);
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
    protected $appends = [
        'batas_pengumpulan_tugas_asesmen_formatted',
        'batas_pembayaran_formatted',
        'tanggal_rincian_asesmen_dibuat_formatted',
        'tanggal_rincian_bayar_dibuat_formatted',
    ];
    protected $casts = [
        'rincianbayar_createdat' => 'string',
        'tgl_bayar_ditutup' => 'string',
        'tugasasesmen_createdat' => 'string',
        'batas_pengumpulan_tugas_asesmen' => 'string',
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
                $dateString = $this->rincianbayar_createdat;
                if (!$dateString) {
                    return 'N/A';
                }

                // Konversi string kembali ke objek Carbon
                $carbonDate = Carbon::parse($dateString);

                if ($carbonDate->isToday()) {
                    return $carbonDate->format('H:i');
                }
                return $carbonDate->format('d M Y');
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
                $dateString = $this->tugasasesmen_createdat;
                if (!$dateString) {
                    return 'N/A';
                }

                // Konversi string kembali ke objek Carbon
                $carbonDate = Carbon::parse($dateString);

                if ($carbonDate->isToday()) {
                    return $carbonDate->format('H:i');
                }
                return $carbonDate->format('d M Y');
            }
        );
    }

    protected function batasPengumpulanTugasAsesmenFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                $batas = $this->batas_pengumpulan_tugas_asesmen;
                if (!$batas) {
                    return '-';
                }
                return Carbon::parse($batas)->locale('id')->isoFormat('D MMMM YYYY, [pukul] HH:mm');
            }
        );
    }
    protected function batasPembayaranFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                $batas = $this->tgl_bayar_ditutup;
                if (!$batas) {
                    return '-';
                }
                return Carbon::parse($batas)->locale('id')->isoFormat('D MMMM YYYY, [pukul] HH:mm');
            }
        );
    }

    protected static function booted(): void
    {
        static::deleting(function (Sertification $sertification) {
            foreach ($sertification->asesmenfiles as $file) {
                if ($file->path_file) {
                    Storage::disk('public')->delete($file->path_file);
                }
                $file->delete();
            }

            foreach ($sertification->news as $pengumuman) {
                $pengumuman->delete();
            }
        });
    }
}
