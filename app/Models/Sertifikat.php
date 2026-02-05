<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Sertifikat extends Model
{
    use LogsActivity;
    protected $guarded = [
        
    ];
    public function asesi()
    {
        return $this->belongsTo(Asesi::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Sertifikat')
            ->setDescriptionForEvent(fn(string $eventName) => "Sertifikat milik {$this->asesi->student->user->name} telah di-{$eventName}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly([
                'nomor_seri',
                'nomor_sertifikat',
                'nomor_registrasi',
                'tanggal_terbit',
                'berlaku_hingga',
                'file_path'
            ]);
    }
}
