<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\Skema;
use App\Models\Student;
use App\Models\Asesi;
use App\Models\Asesifile;
use App\Models\Sertifikat;
use App\Models\News;
use App\Models\Asesmen;

class FileAccessController extends Controller
{
    /**
     * Whitelist tabel yang boleh diakses melalui route download.
     * Key = nama tabel, Value = FQCN Model.
     */
    private const TABLE_MODELS = [
        'skemas'      => Skema::class,
        'students'    => Student::class,
        'asesis'      => Asesi::class,
        'asesi_files' => AsesiFile::class,
        'sertifikats' => Sertifikat::class,
        'news'        => News::class,
        'asesmens'    => Asesmen::class,
    ];

    /**
     * Download private files securely with Policy validation.
     *
     * @param string $table  Nama tabel (harus ada di whitelist TABLE_MODELS)
     * @param string $id     ID record
     * @param string $column Nama kolom yang berisi path file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(string $table, string $id, string $column)
    {
        $modelClass = self::TABLE_MODELS[$table] ?? null;

        if (!$modelClass) {
            abort(404, 'Resource tidak terdaftar.');
        }

        $data = $modelClass::findOrFail($id);

        Gate::authorize('downloadFile', $data);

        $filePath = $data->$column ?? null;

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('local');
        return $storage->response($filePath);
    }
}