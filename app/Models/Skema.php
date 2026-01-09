<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skema extends Model
{
    use HasFactory, SoftDeletes;
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
