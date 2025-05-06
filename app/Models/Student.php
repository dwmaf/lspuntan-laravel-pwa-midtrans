<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class)->where('role', 'asesi');
    }
    public function asesi()
    {
        return $this->hasMany(Asesi::class);
    }
    public function suket_magang()
    {
        return $this->hasMany(SuketMagang::class);
    }
    public function sertif_pelatihan()
    {
        return $this->hasMany(SertifPelatihan::class);
    }
    public function doc_pendukung()
    {
        return $this->hasMany(DocPendukung::class);
    }
}
