<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializesDatesWithoutConversion;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Asesmen extends Model
{
    use LogsActivity, SerializesDatesWithoutConversion;
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
            ->logOnly(['content', 'deadline', 'sertification_id', 'path_file'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function sertification()
    {
        return $this->belongsTo(Sertification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
