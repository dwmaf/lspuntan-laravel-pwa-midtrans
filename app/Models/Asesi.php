<?php

namespace App\Models;

use App\Enums\StatusBerkasAdministrasi;
use App\Enums\StatusAksesMenuAsesmen;
use App\Enums\StatusFinalAsesi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asesi extends Model
{
    use LogsActivity, HasFactory, SoftDeletes;
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
    public function makulnilais()
    {
        return $this->hasMany(Makulnilai::class);
    }
    public function sertification()
    {
        return $this->belongsTo(Sertification::class);
    }
    public function asesifiles()
    {
        return $this->hasMany(Asesifile::class);
    }
    // public function transaction()
    // {
    //     return $this->hasMany(Transaction::class);
    // }
    public function asesiasesmenfiles()
    {
        return $this->hasMany(Asesiasesmenfile::class);
    }
    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class);
    }
    protected static function booted(): void
    {
        static::deleting(function (Asesi $asesi) {
            foreach ($asesi->asesifiles as $asesifile){
                $asesifile->delete();
            }
            foreach ($asesi->asesiasesmenfiles as $asesiasesmenfile){
                $asesiasesmenfile->delete();
            }
            
        });
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Asesi')
            ->setDescriptionForEvent(fn(string $eventName) => "Data asesi {$this->student->user->name} telah di-{$eventName}")
            ->logOnlyDirty()
            ->logOnly([
                'status',
                'sertification_id',
                'catatan_perbaikan',
                'apl_1',
                'apl_2',
                'foto_ktm',
            ]);
    }
}
