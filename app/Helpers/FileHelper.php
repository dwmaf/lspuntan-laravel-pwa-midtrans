<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileHelper
{
    public static function storeFileWithUniqueName(UploadedFile $file, string $baseDirectory): array
    {
        $uniqueId = uniqid() . '-' . now()->timestamp;
        $newFilename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . $uniqueId . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($baseDirectory, $newFilename, 'public');
        return ['path' => $path];
    }
}