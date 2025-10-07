<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pengumumanasesmen extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function sertification()
    {
        return $this->belongsTo(Sertification::class);
    }
    public function pengumumanasesmenfile()
    {
        return $this->hasMany(Pengumumanasesmenfile::class);
    }
    public function pembuatpengumuman()
    {
        // Nama kolom foreign key 'rincian_bayar_dibuat_oleh' tidak standar,
        // jadi kita perlu menentukannya secara eksplisit.
        return $this->belongsTo(User::class, 'rincian_pengumuman_asesmen_dibuat_oleh');
    }

    protected static function booted(): void
    {
        static::deleting(function (PengumumanAsesmen $pengumuman) {
            // Hapus semua file lampiran terkait
            foreach ($pengumuman->pengumumanasesmenfile as $file) {
                if ($file->path_file) {
                    Storage::disk('public')->delete($file->path_file);
                }
                $file->delete(); // Hapus record dari database
            }
        });
    }
}
