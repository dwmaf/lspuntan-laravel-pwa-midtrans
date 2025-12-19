<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // <-- Tambahkan ini
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Sertification extends Model
{
    use LogsActivity, HasFactory;
    /** @use HasFactory<\Database\Factories\SertificationFactory> */
    protected $guarded = [
        
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
    public function paymentInstruction()
    {
        return $this->hasOne(PaymentInstruction::class);
    }

    public function asesmen()
    {
        return $this->hasOne(Asesmen::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Sertification $sertification) {
            if($sertification->asesmen){ 
                $sertification->asesmen->delete();
            }
            if($sertification->paymentInstruction){
                $sertification->paymentInstruction->delete();
            }
            foreach ($sertification->news as $pengumuman) {
                $pengumuman->delete();
            }
            foreach ($sertification->asesis as $asesi) {
                $asesi->delete();
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Sertification')
            ->setDescriptionForEvent(fn(string $eventName) => "Sertification telah di-{$eventName}")
            ->logOnlyDirty()
            ->logOnly([
                'skema_id',
                'asesor_id',
                'tuk',
                'tgl_apply_dibuka',
                'tgl_apply_ditutup',
                'status',
            ]);
    }
}
