<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Asesmen extends Model
{
    use LogsActivity;
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function asesmenfiles()
    {
        return $this->hasMany(Asesmenfile::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Asesmen')
            ->setDescriptionForEvent(fn(string $eventName) => "Asesmen telah di-{$eventName}")
            ->logOnlyDirty()
            ->logOnly([
                'content',
                'sertification_id',
                'deadline',
                'published_at',
            ]);
    }
}
