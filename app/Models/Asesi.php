<?php

namespace App\Models;

use App\Enums\StatusBerkasAdministrasi;
use App\Enums\StatusFinalAsesi;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Asesi extends Model
{
    use LogsActivity, HasFactory;
    /** @use HasFactory<\Database\Factories\AsesiFactory> */
    protected $table = 'asesi';
    protected $guarded = [];
    protected $casts = [
        'status_berkas' => StatusBerkasAdministrasi::class,
        'status_final' => StatusFinalAsesi::class,
    ];
    protected $appends = [
        'status_berkas_label',
        'status_final_label',
    ];
    // fungsi statusberkaslabel dan statusfinallabel mungkin nda dipakai
    protected function statusBerkasLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status_berkas?->label(),
        );
    }

    protected function statusFinalLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status_final?->label(),
        );
    }
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
    public function sertifikasi()
    {
        return $this->belongsTo(Sertifikasi::class, 'sertifikasi_id');
    }
    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'asesor_id');
    }
    public function berkasAsesi()
    {
        return $this->hasMany(BerkasAsesi::class, 'asesi_id');
    }
    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class, 'asesi_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Asesi')
            ->setDescriptionForEvent(fn(string $eventName) => "Data asesi {$this->mahasiswa->user->name} telah di-{$eventName}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly([
                'status_berkas',
                'asesor_id',
                'status_final'
            ]);
    }
}
