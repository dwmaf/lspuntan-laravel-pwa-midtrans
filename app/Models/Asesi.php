<?php

namespace App\Models;

use App\Enums\StatusBerkasAdministrasi;
use App\Enums\StatusAksesMenuAsesmen;
use App\Enums\StatusFinalAsesi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Asesi extends Model
{
    use LogsActivity, HasFactory;
    /** @use HasFactory<\Database\Factories\AsesiFactory> */
    protected $guarded = [];
    protected $casts = [
        'status_berkas' => StatusBerkasAdministrasi::class,
        'status_akses_asesmen' => StatusAksesMenuAsesmen::class,
        'status_final' => StatusFinalAsesi::class,
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function sertification()
    {
        return $this->belongsTo(Sertification::class);
    }
    public function asesifiles()
    {
        return $this->hasMany(Asesifile::class);
    }
    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Asesi')
            ->setDescriptionForEvent(fn(string $eventName) => "Data asesi {$this->student->user->name} telah di-{$eventName}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly([
                'status_berkas',
                'status_akses_asesmen',
                'status_final'
            ]);
    }
}
