<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skema extends Model
{
    use HasFactory;
    /** @use HasFactory<\Database\Factories\SkemaFactory> */
    protected $guarded = [
        
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
