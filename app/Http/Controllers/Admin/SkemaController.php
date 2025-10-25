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
        $skemaData = $request->validate([
            'nama_skema' => 'required',
            'string',
            'max:255',
            'format_apl_1' => 'nullable|file|mimes:docx|max:1024',
            'format_apl_2' => 'nullable|file|mimes:docx|max:1024',
        ]);
        foreach (['format_apl_1', 'format_apl_2'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $skemaData[$fileField] = FileHelper::storeFileWithUniqueName($request->file($fileField), 'apl_files')['path'];
            }
        }
        Skema::create($skemaData);
        return redirect(route('admin.skema.create'))->with('message', 'Berhasil Simpan data skema');
    }

    public function update(Request $request, $id)
    {
        $skema = Skema::find($id);
        $request->validate([
            'nama_skema' => ['required', 'string', 'max:255'],
            'format_apl_1' => 'nullable|file|mimes:docx,pdf|max:2048',
            'format_apl_2' => 'nullable|file|mimes:docx,pdf|max:2048',
            'delete_files' => 'nullable|array',
        ]);
        $skema->fill($request->only([
            'nama_skema',
        ]));
        if ($request->has('delete_files')) {
            foreach ($request->delete_files as $fieldName) {
                if ($skema->$fieldName) {
                    Storage::disk('public')->delete($skema->$fieldName);
                    $skema->$fieldName = null;
                }
            }
        }
        foreach (['format_apl_1', 'format_apl_2'] as $fileField) {
            if ($request->hasFile($fileField)) {
                if ($skema->$fileField && Storage::disk('public')->exists($skema->$fileField)) {
                    Storage::disk('public')->delete($skema->$fileField);
                }
                $skema->$fileField = FileHelper::storeFileWithUniqueName($request->file($fileField), 'apl_files')['path'];
            }
        }
        if ($skema->isDirty()) {
            $skema->save();
        }
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
