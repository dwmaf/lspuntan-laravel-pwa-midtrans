<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Newsfile extends Model
{
    use LogsActivity;
    protected $guarded = [
        
    ];
    public function announcement()
    {
        return $this->belongsTo(News::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('File Pengumuman')
            ->setDescriptionForEvent(fn(string $eventName) => "File lampiran untuk pengumuman (ID: {$this->news_id}) telah di-{$eventName}")
            ->logOnlyDirty()
            ->logOnly([
                'path_file',
            ]);
    }
}
