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
        return $this->belongsTo(Asesi::class);
    }
    public function pengumumanasesmenfile()
    {
        return $this->hasMany(Asesi::class);
    }
}
