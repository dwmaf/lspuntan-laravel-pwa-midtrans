<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsfile extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function announcement()
    {
        return $this->belongsTo(News::class);
    }
}
