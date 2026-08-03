<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\StatusSertifikasi;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Sertifikasi extends Model
{
    use LogsActivity, HasFactory;
    /** @use HasFactory<\Database\Factories\SertifikasiFactory> */
    protected $table = 'sertifikasi';
    protected $guarded = [];
    protected $casts = [
        // karena dijadiin casts, maka jika melakukan if harus pakai value, misalnya
        // if ($sertification->status->value === StatusSertifikasi::SELESAI->value)
        'status' => StatusSertifikasi::class, // Supaya jadi Enum Object
    ];
    public function asesor()
    {
        return $this->belongsToMany(Asesor::class, 'asesor_sertifikasi', 'sertifikasi_id', 'asesor_id');
    }
    public function skema()
    {
        return $this->belongsTo(Skema::class, 'skema_id');
    }
    public function asesi()
    {
        return $this->hasMany(Asesi::class, 'sertifikasi_id');
    }
    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'sertifikasi_id');
    }

    public function asesmen()
    {
        return $this->hasMany(Asesmen::class, 'sertifikasi_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Sertifikasi')
            ->setDescriptionForEvent(fn(string $eventName) => "Jadwal Sertifikasi {$this->skema->nama_skema} telah di-{$eventName}")
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
