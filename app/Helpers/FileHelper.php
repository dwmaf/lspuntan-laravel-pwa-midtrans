<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    /**
     * Menangani upload file tunggal untuk sebuah field pada model.
     * Jika ada file lama, file tersebut akan dihapus.
     */
    public static function handleSingleFileUploads(Model $model, array $fieldNames, Request $request, string $directory): void
    {
        foreach ($fieldNames as $fileField) {
            if ($request->hasFile($fileField)) {
                $model->$fileField = self::storeFileWithUniqueName($request->file($fileField), $directory)['path'];
            }
        }
    }

    /**
     * Menangani upload banyak file yang disimpan sebagai relasi terpisah.
     */
    public static function handleCollectionFileUploads(string $relatedModelClass, string $foreignKey, int $foreignId, Request $request, array $requestFieldNames, string $directory): void
    {
        foreach ($requestFieldNames as $fieldName) {
            if ($request->hasFile($fieldName)) {
                foreach ($request->file($fieldName) as $file) {
                    $filePath = self::storeFileWithUniqueName($file, $directory)['path'];
                    $relatedModelClass::create([$foreignKey => $foreignId,'path_file' => $filePath,'type' => $fieldName,]);
                }
            }
        }
    }

    /**
     * Menghapus file tunggal dari sebuah field pada model.
     */
    public static function handleSingleFileDeletes(Model $model, array $fieldNames): void
    {
        if (empty($fieldNames)) return;
        foreach ($fieldNames as $fieldName) {
            if ($model->$fieldName && Storage::disk('public')->exists($model->$fieldName)) {
                Storage::disk('public')->delete($model->$fieldName);
                $model->$fieldName = null;
            }
        }
    }

    /**
     * Menghapus banyak file dari model relasi berdasarkan ID.
     */
    public static function handleCollectionFileDeletes(string $relatedModelClass, array $fileIds): void
    {
        if (empty($fileIds)) return;
        $filesToDelete = $relatedModelClass::whereIn('id', $fileIds)->get();
        foreach ($filesToDelete as $file) {
            if (Storage::disk('public')->exists($file->path_file)) {
                Storage::disk('public')->delete($file->path_file);
            }
            $file->delete();
        }
    }

    public static function saveIfDirty(array $models): void
    {
        foreach ($models as $model) {
            if ($model->isDirty()) {
                $model->save();
            }
        }
    }
}