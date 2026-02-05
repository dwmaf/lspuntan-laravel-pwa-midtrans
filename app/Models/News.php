<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializesDatesWithoutConversion;

class News extends Model
{
    use SerializesDatesWithoutConversion;
    protected $guarded = [
        
    ];
    public function sertification()
    {
        return $this->belongsTo(Sertification::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
