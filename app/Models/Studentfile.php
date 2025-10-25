<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Studentfile extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
