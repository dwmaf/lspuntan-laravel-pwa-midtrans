<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Praasesmenfile extends Model
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
}
