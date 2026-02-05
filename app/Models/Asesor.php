<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Asesor extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [
        
    ];
    public function skemas()
    {
        return $this->belongsToMany(Skema::class, 'asesor_skema');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function sertifications()
    {
        return $this->belongsToMany(Sertification::class, 'asesor_sertification');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Asesor')
            ->setDescriptionForEvent(fn(string $eventName) => "Data Asesor {$this->user->name} telah di-{$eventName}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly([                
                'no_reg_met',
                'masa_berlaku_sertif_teknis',
                'masa_berlaku_sertif_asesor'
            ]);
    }
}
