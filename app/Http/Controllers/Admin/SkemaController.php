<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Helpers\FileHelper;
use Inertia\Inertia;

class SkemaController extends Controller
{
    public function create(Request $request)
    {
        $skemas = Skema::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('nama_skema', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->onEachSide(0)
            ->withQueryString();

        return Inertia::render('Admin/SkemaSertifAdmin', [
            'skemas' => $skemas,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate( [
            'nama_skema' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'format_apl_1' => 'nullable|file|mimes:docx|max:2048',
            'format_apl_2' => 'nullable|file|mimes:docx|max:2048',
            'format_asesmen' => 'nullable|file|mimes:zip,rar|max:5120',
        ]);

        $skema = new Skema($validatedData);
        $skema->is_active = $request->boolean('is_active', true);
        FileHelper::handleSingleFileUploads($skema, ['format_apl_1','format_apl_2','format_asesmen'], $request, 'apl_files');
        $skema->save();

        return redirect(route('admin.skema.create'))->with('message', 'Berhasil Simpan data skema');
    }

    public function update(Request $request, Skema $skema)
    {
        $request->validate([
            'nama_skema' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'delete_files' => 'nullable|array',
            'format_apl_1' => 'nullable|file|mimes:doc,docx,pdf|max:2048',
            'format_apl_2' => 'nullable|file|mimes:doc,docx,pdf|max:2048',
            'format_asesmen' => 'nullable|file|mimes:zip,rar|max:5120',
        ]);

        $skema->fill($request->only(['nama_skema']));
        $skema->is_active = $request->boolean('is_active');

        FileHelper::handleSingleFileDeletes($skema, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($skema, ['format_apl_1','format_apl_2','format_asesmen'], $request, 'apl_files');
        FileHelper::saveIfDirty([$skema]);

        return redirect(route('admin.skema.create'))->with('message', 'Berhasil update data skema');
    }

    public function destroy(Request $request, Skema $skema)
    {
        if ($skema->sertifications()->exists()) {
            return redirect(route('admin.skema.create'))->with('error', 'Skema tidak bisa dihapus karena sudah memiliki riwayat sertifikasi. Silakan non-aktifkan saja statusnya.');
        }

        FileHelper::handleSingleFileDeletes($skema, ['format_apl_1','format_apl_2','format_asesmen']);
        $skema->delete();
        return redirect(route('admin.skema.create'))->with('message', 'Skema berhasil dihapus');
    }
}
