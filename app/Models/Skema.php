<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skema extends Model
{
    /** @use HasFactory<\Database\Factories\SkemaFactory> */
    use HasFactory;
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function asesors()
    {
        return $this->belongsToMany(Asesor::class, 'asesor_skema');
    }
    public function sertifications()
    {
        return $this->hasMany(Sertification::class);
    }
}
