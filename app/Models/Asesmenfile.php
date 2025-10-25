<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asesmenfile extends Model
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
}
