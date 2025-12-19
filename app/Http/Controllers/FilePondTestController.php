<?php

namespace App\Http\Controllers;

use App\Models\FilePondTest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class FilePondTestController extends Controller
{
    public function index()
    {
        $tests = FilePondTest::latest()->get();
        return Inertia::render('FilePondTest/Index', [
            'tests' => $tests
        ]);
    }

    public function create()
    {
        return Inertia::render('FilePondTest/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240',
            'dev_files' => 'nullable|array', // Validasi untuk dev files
            'dev_files.*' => 'file|max:10240',
        ]);

        $paths = [];
        
        // Handle Standard FilePond Files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $paths[] = $file->store('filepond-tests', 'public');
            }
        }

        // Handle Dev Files (New Implementation)
        if ($request->hasFile('dev_files')) {
            foreach ($request->file('dev_files') as $file) {
                $paths[] = $file->store('filepond-tests', 'public');
            }
        }
        
        // Handle Dev Single File
        if($request->hasFile('dev_single_file')){
             $paths[] = $request->file('dev_single_file')->store('filepond-tests', 'public');
        }

        FilePondTest::create([
            'title' => $request->title,
            'file_path' => $paths,
        ]);

        return redirect()->route('filepond-test.index')->with('success', 'Data created successfully');
    }

    public function edit(FilePondTest $filepond_test)
    {
        return Inertia::render('FilePondTest/Edit', [
            'test' => $filepond_test
        ]);
    }

    public function update(Request $request, FilePondTest $filepond_test)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'files' => 'nullable|array',
            'dev_files' => 'nullable|array',
            'dev_delete_files' => 'nullable|array',
        ]);

        $currentFiles = $filepond_test->file_path ?? [];
        $finalPaths = $currentFiles;

        // --- 1. Handle Deletions (Dev Input) ---
        // `dev_delete_files` berisi ID/Index file yang ingin dihapus. 
        // Note: Di Vue kita mapping ID file existing sebagai index array atau string ID.
        // Jika ID adalah index array:
        if ($request->has('dev_delete_files')) {
           $indicesToDelete = $request->input('dev_delete_files');
           // Filter output logic: Keep files NOT in the deletion list by index
           // Note: Because indices shift when untsetting, we better use specific value matching if IDs were UUIDs.
           // Since we used array index as ID in Vue: `id: index`
           
           $tempPaths = [];
           foreach($finalPaths as $index => $path) {
               if(!in_array($index, $indicesToDelete)) {
                   $tempPaths[] = $path;
               } else {
                   // Delete file physically
                   if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                   }
               }
           }
           $finalPaths = $tempPaths;
        }

        // --- 2. Handle New Uploads (Dev Input) ---
        if ($request->hasFile('dev_files')) {
            foreach ($request->file('dev_files') as $file) {
                $finalPaths[] = $file->store('filepond-tests', 'public');
            }
        }


        // --- 3. Handle Standard FilePond Update Logic (Mix) ---
        // Note: The original controller logic for FilePond used a 'sync' approach where 
        // frontend sent ALL valid file paths (old and new).
        // Dev logic above is 'incremental' (add new, delete specific).
        // To prevent conflicts, we only run standard logic if standard input 'files' is present AND dev inputs are empty, 
        // OR we just append standard logic results if intended to work together.
        // For this Demo, we assume user uses EITHER Dev OR Standard. 
        // But let's keep standard logic safe:
        
        if ($request->has('files')) {
            $newFiles = $request->input('files', []);
            $standardFinalPaths = [];

            foreach ($newFiles as $file) {
                if (str_starts_with($file, 'tmp/')) {
                    $newPath = str_replace('tmp/', 'filepond-tests/', $file);
                    if (Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->move($file, $newPath);
                        $standardFinalPaths[] = $newPath;
                    }
                } else {
                    if (in_array($file, $currentFiles)) {
                        $standardFinalPaths[] = $file;
                    }
                }
            }
            // If using standard input, it dictates the final state likely
            // We would need to decide which source of truth wins. 
            // For safety in this hybrid test, let's just MERGE unique paths if both are used.
            $finalPaths = array_unique(array_merge($finalPaths, $standardFinalPaths));
        }

        $filepond_test->update([
            'title' => $request->title,
            'file_path' => array_values($finalPaths), // Re-index array
        ]);

        return redirect()->route('filepond-test.index')->with('success', 'Data updated successfully');
    }

    public function processFile(Request $request)
    {
        if ($request->hasFile('files')) {
            $file = $request->file('files')[0];
            $path = $file->store('tmp', 'public');
            return response($path, 200)->header('Content-Type', 'text/plain');
        }

        return response('No file uploaded.', 400);
    }

    public function revertFile(Request $request)
    {
        $filePath = $request->getContent();
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        return response()->noContent();
    }

    public function loadFile(Request $request)
    {
        $path = $request->query('load');
        if(!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }
        return response()->file(Storage::disk('public')->path($path));
    }

    public function destroy(FilePondTest $filePondTest)
    {
        $paths = $filePondTest->file_path;

        if ($paths) {
            foreach ($paths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
        $filePondTest->delete();

        return redirect()->route('filepond-test.index')->with('success', 'Data deleted successfully');
    }
}
