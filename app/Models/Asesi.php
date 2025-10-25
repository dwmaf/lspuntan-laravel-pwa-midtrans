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
    public function makulnilais()
    {
        return $this->hasMany(Makulnilai::class);
    }
    public function sertification()
    {
        return $this->belongsTo(Sertification::class);
    }
    public function asesifiles()
    {
        return $this->hasMany(Asesifile::class);
    }
    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
    public function asesiasesmenfiles()
    {
        return $this->hasMany(Asesiasesmenfile::class);
    }
    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class);
    }
}
