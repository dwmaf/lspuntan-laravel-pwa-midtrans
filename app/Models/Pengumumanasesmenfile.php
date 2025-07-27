<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumumanasesmenfile extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function pengumumanasesmen()
    {
        return $this->belongsTo(Pengumumanasesmen::class);
    }
}
