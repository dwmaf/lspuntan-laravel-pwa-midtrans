<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesor extends Model
{
    /** @use HasFactory<\Database\Factories\AsesorFactory> */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function skemas()
    {
        return $this->belongsToMany(Skema::class, 'asesor_skema');
    }
    public function user()
    {
        return $this->belongsTo(User::class)->where('role', 'asesor');
    }
    public function sertifications()
    {
        return $this->hasMany(Sertification::class);
    }
}
