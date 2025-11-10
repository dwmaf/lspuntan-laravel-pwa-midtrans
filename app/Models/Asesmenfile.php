<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asesmenfile extends Model
{
    protected $guarded = [
        
    ];
    public function sertification()
    {
        return $this->belongsTo(Asesi::class);
    }
}
