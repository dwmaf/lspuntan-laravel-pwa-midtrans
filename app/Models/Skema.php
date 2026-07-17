<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Skema extends Model
{
    use HasFactory, LogsActivity;
    /** @use HasFactory<\Database\Factories\SkemaFactory> */
    protected $table = 'skema';
    protected $guarded = [];
    public function asesor()
    {
        return $this->belongsToMany(Asesor::class, 'asesor_skema', 'skema_id', 'asesor_id');
    }
    public function sertifikasi()
    {
        return $this->hasMany(Sertifikasi::class, 'skema_id');
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
