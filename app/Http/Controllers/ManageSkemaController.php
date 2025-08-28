<?php

namespace App\Http\Controllers;

use App\Models\Skema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Helpers\FileHelper;
class ManageSkemaController extends Controller
{
    private function storeFileWithUniqueName(UploadedFile $file, string $baseDirectory): array
    {
        // id unik berdasarkan timestamp
        $uniqueId = uniqid().'-'.now()->timestamp;
        // nama file asli tanpa extension dijadiin slug + unik id + ekstensinya tadi
        $newFilename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . $uniqueId . '.' . $file->getClientOriginalExtension();
        // Simpan file dengan nama baru
        $path = $file->storeAs($baseDirectory, $newFilename, 'public');
        return ['path' => $path];
    }
    // nampilin halaman untuk nambah skema sertifikasi
    public function create()
    {
        return view('admin.skema.create-skema',[
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
                $fileData = $this->storeFileWithUniqueName($request->file($fileField), 'apl_files');
                $skemaData[$fileField] = $fileData['path'];
            }
        }
        Skema::create($skemaData);
        return redirect(route('admin.skema.create'))->with('success', 'Berhasil Simpan data skema');
    }

    // untuk nampilin halaman untuk mengedit sertifikasi
    public function edit(Request $request, $id)
    {
        return view('admin.skema.edit-skema',[
            'skema'=>Skema::find($id)
        ]);
    }

    // untuk mengupdate skema yg tadi diedit
    public function update(Request $request, $id)
    {
        $skema = Skema::find($id);
        $request->validate([
            'nama_skema' => ['required', 'string','max:255'],
        ]);
        $skema->fill($request->only([
            'nama_skema',
        ]));
        foreach (['format_apl_1', 'format_apl_2'] as $fileField) {
            if ($request->hasFile($fileField)) {
                if ($skema->$fileField && Storage::disk('public')->exists($skema->$fileField)) {
                    Storage::disk('public')->delete($skema->$fileField);
                }
                $fileData = $this->storeFileWithUniqueName($request->file($fileField), 'apl_files');
                $skema->$fileField = $fileData['path'];
            }
        }
        if ($skema->isDirty()) {
            $skema->save();
        }
        return redirect(route('admin.skema.create'))->with('success', 'Berhasil update data skema');
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
        return redirect(route('admin.skema.create'))->with('success', 'Skema berhasil dihapus');
    }
}
