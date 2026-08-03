<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Sertifikat extends Model
{
    use LogsActivity;
    protected $table = 'sertifikat';
    protected $guarded = [];
    public function asesi()
    {
        return $this->belongsTo(Asesi::class, 'asesi_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Sertifikat')
            ->setDescriptionForEvent(fn(string $eventName) => "meng-{$eventName} data sertifikat milik {$this->asesi->mahasiswa->user->name}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly([
                'asesi_id',
                'nomor_seri',
                'nomor_sertifikat',
                'nomor_registrasi',
                'tanggal_terbit',
                'berlaku_hingga',
            ]);
    }
}
