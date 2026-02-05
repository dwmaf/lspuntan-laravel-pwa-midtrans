<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializesDatesWithoutConversion;
use Illuminate\Support\Facades\Storage;

class Asesmen extends Model
{
    use SerializesDatesWithoutConversion;
    protected $guarded = [
        
    ];
}
