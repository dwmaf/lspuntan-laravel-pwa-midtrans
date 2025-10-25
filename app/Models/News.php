<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class News extends Model
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
    public function newsfiles()
    {
        return $this->hasMany(Newsfile::class);
    }
    public function pembuatpengumuman()
    {
        // Nama kolom foreign key 'rincian_bayar_dibuat_oleh' tidak standar,
        // jadi kita perlu menentukannya secara eksplisit.
        return $this->belongsTo(User::class, 'rincian_pengumuman_asesmen_dibuat_oleh');
    }

    protected static function booted(): void
    {
        static::deleting(function (News $news) {
            // Hapus semua file lampiran terkait
            foreach ($news->newsfile as $file) {
                if ($file->path_file) {
                    Storage::disk('public')->delete($file->path_file);
                }
                $file->delete();
            }
        });
    }
}
