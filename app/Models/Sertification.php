<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
