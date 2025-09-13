<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asesiattachmentfile extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function asesi()
    {
        return $this->belongsTo(Asesi::class);
    }
}
