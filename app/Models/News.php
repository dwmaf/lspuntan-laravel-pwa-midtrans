<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class News extends Model
{
    use LogsActivity;
    protected $guarded = [
        
    ];
    public function sertification()
    {
        return $this->belongsTo(Sertification::class);
    }
    public function newsfiles()
    {
        return $this->hasMany(Newsfile::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (News $news) {
            foreach ($news->newsfile as $file) {
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
            ->useLogName('Pengumuman')
            ->setDescriptionForEvent(fn(string $eventName) => "Sebuah pengumuman telah di-{$eventName}")
            ->logOnlyDirty()
            ->logOnly([
                'rincian',
                'sertification_id',
                'madeby',
            ]);
    }
}
