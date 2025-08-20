<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
