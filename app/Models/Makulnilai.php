<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makulnilai extends Model
{
    protected $guarded = [
        
    ];
    
    public function asesi()
    {
        return $this->belongsTo(Student::class);
    }
}
