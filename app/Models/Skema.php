<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Skema extends Model
{
    use HasFactory, LogsActivity;
    /** @use HasFactory<\Database\Factories\SkemaFactory> */
    protected $guarded = [
        
    ];
    public function asesors()
    {
        return $this->belongsToMany(Asesor::class, 'asesor_skema');
    }
    public function sertifications()
    {
        return $this->hasMany(Sertification::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Skema')
            ->setDescriptionForEvent(fn(string $eventName) => "Skema {$this->nama_skema} telah di-{$eventName}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly([
                'nama_skema',
                'is_active',
                'format_apl_1',
                'format_apl_2',
                'format_asesmen',
            ]);
    }
}
