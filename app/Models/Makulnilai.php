<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makulnilai extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    
    public function asesi()
    {
        return $this->belongsTo(Student::class);
    }
}
