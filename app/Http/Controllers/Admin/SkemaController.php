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
    public function create()
    {
        return Inertia::render('Admin/SkemaSertifAdmin', [
            'skemas' => Skema::all()
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_skema' => 'required',
            'string',
            'max:255',
            'format_apl_1' => 'nullable|file|mimes:docx|max:1024',
            'format_apl_2' => 'nullable|file|mimes:docx|max:1024',
        ]);
        $skema = new Skema($validatedData);
        FileHelper::handleSingleFileUploads($skema, ['format_apl_1', 'format_apl_2'], $request, 'apl_files');
        $skema->save();
        return redirect(route('admin.skema.create'))->with('message', 'Berhasil Simpan data skema');
    }

    public function update(Request $request, $id)
    {
        $skema = Skema::findOrFail($id);
        $validatedData = $request->validate([
            'nama_skema' => ['required', 'string', 'max:255'],
            'format_apl_1' => 'nullable|file|mimes:docx,pdf|max:2048',
            'format_apl_2' => 'nullable|file|mimes:docx,pdf|max:2048',
            'delete_files' => 'nullable|array',
        ]);
        $skema->fill($validatedData);
        FileHelper::handleSingleFileDeletes($skema, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($skema, ['format_apl_1', 'format_apl_2'], $request, 'apl_files');
        FileHelper::saveIfDirty([$skema]);
        
        return redirect(route('admin.skema.create'))->with('message', 'Berhasil update data skema');
    }

    public function destroy(Request $request, $id)
    {
        $skema = Skema::findOrFail($id);
        foreach (['format_apl_1', 'format_apl_2'] as $fileField) {
            if ($skema->$fileField && Storage::disk('public')->exists($skema->$fileField)) {
                Storage::disk('public')->delete($skema->$fileField);
            }
        }
        Skema::destroy($id);
        return redirect(route('admin.skema.create'))->with('message', 'Skema berhasil dihapus');
    }
}
