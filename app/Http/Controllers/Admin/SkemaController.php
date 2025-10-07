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
    
    // nampilin halaman untuk nambah skema sertifikasi
    public function create()
    {
        return Inertia::render('Admin/SkemaSertifAdmin',[
            'skemas'=>Skema::all()
        ]);
    }

    // untuk nyimpan skema yg udh dibuat
    public function store(Request $request)
    {
        // dd($request);
        $skemaData = $request->validate([
            'nama_skema' => 'required', 'string', 'max:255',
            'format_apl_1' => 'file|mimes:docx|max:1024',
            'format_apl_2' => 'file|mimes:docx|max:1024',
        ]);
        foreach (['format_apl_1', 'format_apl_2'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $fileData = FileHelper::storeFileWithUniqueName($request->file($fileField), 'apl_files');
                $skemaData[$fileField] = $fileData['path'];
            }
        }
        Skema::create($skemaData);
        return redirect(route('admin.skema.create'))->with('message', 'Berhasil Simpan data skema');
    }


    // untuk mengupdate skema yg tadi diedit
    public function update(Request $request, $id)
    {
        $skema = Skema::find($id);
        $request->validate([
            'nama_skema' => ['required', 'string','max:255'],
            'format_apl_1' => 'nullable|file|mimes:docx,pdf|max:2048',
            'format_apl_2' => 'nullable|file|mimes:docx,pdf|max:2048',
        ]);
        $skema->fill($request->only([
            'nama_skema',
        ]));
        foreach (['format_apl_1', 'format_apl_2'] as $fileField) {
            if ($request->hasFile($fileField)) {
                if ($skema->$fileField && Storage::disk('public')->exists($skema->$fileField)) {
                    Storage::disk('public')->delete($skema->$fileField);
                }
                $fileData = FileHelper::storeFileWithUniqueName($request->file($fileField), 'apl_files');
                $skema->$fileField = $fileData['path'];
            }
        }
        if ($skema->isDirty()) {
            $skema->save();
        }
        return redirect(route('admin.skema.create'))->with('message', 'Berhasil update data skema');
    }

    // untuk menghapus skema
    public function destroy(Request $request, $id)
    {
        $skema = Skema::find($id);
        foreach (['format_apl_1', 'format_apl_2'] as $fileField) {
            if ($skema->$fileField && Storage::disk('public')->exists($skema->$fileField)) {
                Storage::disk('public')->delete($skema->$fileField);
            }
        }
        Skema::destroy($id);
        return redirect(route('admin.skema.create'))->with('message', 'Skema berhasil dihapus');
    }
}
