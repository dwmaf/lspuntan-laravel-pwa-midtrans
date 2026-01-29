<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asesiasesmen extends Model
{
    protected $guarded = [
        
    ];
    public function asesi()
    {
        return $this->belongsTo(Asesi::class);
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            $fields = ['ak_1', 'ak_2', 'ak_3', 'ak_4', 'ac_1', 'map_1', 'ia_1', 'ia_2', 'ia_5', 'ia_6', 'ia_7'];
            foreach ($fields as $field) {
                if ($model->$field && \Illuminate\Support\Facades\Storage::disk('public')->exists($model->$field)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($model->$field);
                }
            }
        });
    }
}
