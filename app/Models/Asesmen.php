<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\SerializesDatesWithoutConversion;
use Illuminate\Support\Facades\Storage;

class Asesmen extends Model
{
    use LogsActivity, SerializesDatesWithoutConversion;
    protected $guarded = [
        
    ];
    public function asesmenfiles()
    {
        return $this->hasMany(Asesmenfile::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Asesmen $asesmen) {
            foreach ($asesmen->asesmenfiles as $file) {
                if ($file->path_file) {
                    Storage::disk('public')->delete($file->path_file);
                }
                $file->delete();
            }
        });
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
