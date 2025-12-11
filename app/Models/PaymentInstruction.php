<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\SerializesDatesWithoutConversion;

class PaymentInstruction extends Model
{
    use LogsActivity, HasFactory, SerializesDatesWithoutConversion;
    protected $guarded = [
        
    ];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Instruksi Pembayaran')
            ->setDescriptionForEvent(fn(string $eventName) => "Instruksi Pembayaran telah di-{$eventName}")
            ->logOnlyDirty()
            ->logOnly([
                'content',
                'sertification_id',
                'deadline',
                'biaya',
                'published_at',
            ]);
    }
}
