<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesor extends Model
{
    /** @use HasFactory<\Database\Factories\AsesorFactory> */
    use HasFactory;
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function skemas()
    {
        return $this->belongsToMany(Skema::class, 'asesor_skema');
    }
}
