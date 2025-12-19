<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Storage::disk('public')->delete(
    collect(Storage::disk('public')->files('tmp'))
        ->filter(fn($file) => Storage::disk('public')->lastModified($file) < now()->subDay()->getTimestamp())
        ->all()
);