<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializesDatesWithoutConversion;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class News extends Model
{
    use LogsActivity, SerializesDatesWithoutConversion;
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Pengumuman')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'membuat pengumuman',
                'updated' => 'mengedit pengumuman',
                'deleted' => 'menghapus pengumuman',
                default => $eventName,
            })
            ->logOnly(['content', 'path_file', 'sertification_id'])
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
