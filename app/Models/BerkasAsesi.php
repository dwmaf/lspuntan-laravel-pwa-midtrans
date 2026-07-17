<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BerkasAsesi extends Model
{
    protected $table = 'berkas_asesi';
    protected $guarded = [];
    public function asesi()
    {
        return $this->belongsTo(Asesi::class, 'asesi_id');
    }
}
