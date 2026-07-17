<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializesDatesWithoutConversion;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Asesmen extends Model
{
    use LogsActivity, SerializesDatesWithoutConversion;
    protected $table = 'asesmen';
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Tugas Asesmen')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'membuat instruksi asesmen',
                'updated' => 'mengedit instruksi asesmen',
                'deleted' => 'menghapus instruksi asesmen',
                default => $eventName,
            })
            ->logOnly(['content', 'deadline', 'sertifikasi_id', 'path_file'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function sertifikasi()
    {
        return $this->belongsTo(Sertifikasi::class, 'sertifikasi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
