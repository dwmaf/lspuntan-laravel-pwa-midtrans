<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocPendukung extends Model
{
    /** @use HasFactory<\Database\Factories\DocPendukungFactory> */
    use HasFactory;
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
