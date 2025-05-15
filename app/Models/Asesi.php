<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesi extends Model
{
    /** @use HasFactory<\Database\Factories\AsesiFactory> */
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
        return $this->belongsTo(Sertification::class);
    }
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
