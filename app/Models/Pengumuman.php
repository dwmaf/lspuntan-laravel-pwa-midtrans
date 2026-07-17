<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializesDatesWithoutConversion;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pengumuman extends Model
{
    use LogsActivity, SerializesDatesWithoutConversion;
    protected $table = 'pengumuman';
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
            ->logOnly(['content', 'path_file', 'sertifikasi_id'])
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
