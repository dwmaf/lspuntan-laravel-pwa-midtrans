<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    protected $guarded = [
        
    ];
    public function asesi()
    {
        return $this->belongsTo(Asesi::class);
    }
}
