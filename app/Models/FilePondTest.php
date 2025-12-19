<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilePondTest extends Model
{
    protected $guarded = [];

    protected $casts = ['file_path' => 'array',];
}
