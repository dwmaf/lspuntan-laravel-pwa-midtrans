<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Boot the model.
     * Method ini akan secara otomatis dijalankan oleh Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        // Menggunakan event 'created' yang berjalan SETELAH record disimpan ke DB.
        // Ini memastikan kita sudah memiliki $transaction->id untuk digunakan.
        static::created(function ($transaction) {
            // Cek untuk memastikan kita tidak menimpa nomor invoice yang sudah ada.
            if (is_null($transaction->invoice_number)) {
                $prefix = 'LSP';
                $year = $transaction->created_at->format('Y');
                $month = $transaction->created_at->format('m');
                $userId = $transaction->asesi?->student?->user_id ?? 0;

                // Buat nomor invoice dan simpan kembali ke database tanpa memicu event lagi.
                $transaction->invoice_number = "{$year}-{$month}-{$prefix}-{$userId}{$transaction->id}";
                $transaction->saveQuietly();
            }
        });
    }

    public function asesi()
    {
        return $this->belongsTo(Asesi::class);
    }
}
