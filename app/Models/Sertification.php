<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\StatusSertifikasi;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Sertification extends Model
{
    use LogsActivity, HasFactory;
    /** @use HasFactory<\Database\Factories\SertificationFactory> */
    protected $guarded = [
        
    ];
    protected $casts = [
        'status' => StatusSertifikasi::class, // Supaya jadi Enum Object
        // 'tgl_apply_dibuka' => 'date',
        // 'tgl_apply_ditutup' => 'date',
        // 'jadwal_test' => 'datetime',
    ];
    public function asesors()
    {
        return $this->belongsToMany(Asesor::class, 'asesor_sertification');
    }
    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }
    public function asesis()
    {
        return $this->hasMany(Asesi::class);
    }
    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function asesmen()
    {
        return $this->hasOne(Asesmen::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Sertification')
            ->setDescriptionForEvent(fn(string $eventName) => "Jadwal Sertifikasi skema {$this->skema->nama_skema} telah di-{$eventName}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly([
                'tuk',
                'biaya',
                'no_rek',
                'bank',
                'atas_nama_rek',
                'tgl_apply_dibuka',
                'tgl_apply_ditutup',
                'tgl_asesmen_mulai',
                'tgl_asesmen_selesai',
                'status',
            ]);
    }
}
