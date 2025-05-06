<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesi extends Model
{
    /** @use HasFactory<\Database\Factories\AsesiFactory> */
    use HasFactory;
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function sertifications()
    {
        return $this->belongsToMany(Sertification::class, 'asesi_sertification');
    }
}
