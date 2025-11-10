<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesor extends Model
{
    protected $guarded = [
        
    ];
    public function skemas()
    {
        return $this->belongsToMany(Skema::class, 'asesor_skema');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function sertifications()
    {
        return $this->belongsToMany(Sertification::class, 'asesor_sertification');
    }
}
