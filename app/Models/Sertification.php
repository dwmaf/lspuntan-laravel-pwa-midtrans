<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sertification extends Model
{
    /** @use HasFactory<\Database\Factories\SertificationFactory> */
    use HasFactory;
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
        return $this->hasOne(Skema::class);
    }
}
