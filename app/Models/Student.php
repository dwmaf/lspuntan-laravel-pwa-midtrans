<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class)->where('role', 'asesi');
    }
    public function asesi()
    {
        return $this->hasMany(Asesi::class);
    }
}
