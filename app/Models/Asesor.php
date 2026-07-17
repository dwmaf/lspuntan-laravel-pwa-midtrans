<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Asesor extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'asesor';
    protected $guarded = [];
    public function skema()
    {
        return $this->belongsToMany(Skema::class, 'asesor_skema', 'asesor_id', 'skema_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function sertifikasi()
    {
        return $this->belongsToMany(Sertifikasi::class, 'asesor_sertifikasi', 'asesor_id', 'sertifikasi_id');
    }
    public function asesi()
    {
        return $this->hasMany(Asesi::class, 'asesor_id');
    }
    public function tapActivity(Activity $activity)
    {
        $activity->properties = $activity->properties->merge([
            'asesor_user_name' => $this->user?->name,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Asesor')
            ->setDescriptionForEvent(fn(string $eventName) => "Data Asesor {$this->user?->name} telah di-{$eventName}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly([                
                'no_reg_met',
                'masa_berlaku_sertif_teknis',
                'masa_berlaku_sertif_asesor',
                'is_active',
            ]);
    }
}
