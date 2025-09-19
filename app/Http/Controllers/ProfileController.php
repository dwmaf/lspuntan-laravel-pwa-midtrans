<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\Student;
use App\Models\Studentattachmentfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Helpers\FileHelper;
class ProfileController extends Controller
{
    
    
    // buat nampilin halaman edit profile dari sisi admin
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
            'student' => $request->user()->student()
        ]);
    }
    // buat nampilin halaman edit profile dari sisi asesi
    public function edit_asesi(Request $request): View
    {
        $user = $request->user()->load('student.studentattachmentfiles');
        return view('asesi.profile.edit', [
            'user'=>$user,
            'student' => $user->student
        ]);
    }
    // buat mengupdate profile yg tadi diedit dari sisi admin
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty(['email','no_tlp_hp'])) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return back()->with('success', 'Profil berhasil diperbaharui');
    }
    // buat mengupdate profile yg tadi diedit dari sisi asesi
    public function update_asesi(Request $request)
    {
        // $student = $request->user()->student;
        $student = Student::with('user')->find($request->id);
        $user = $student->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'tmpt_lhr' => 'required|string|max:255',
            'tgl_lhr' => 'required|string|max:255',
            'kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'kebangsaan' => 'required|string|max:255',
            'no_tlp_hp' => 'required|string|max:255',
            'kualifikasi_pendidikan' => 'required|string|max:255',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktm' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pas_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'kartu_hasil_studi' => 'nullable|array|max:5', // Memastikan input adalah array dan maksimal 5 item
            'kartu_hasil_studi.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072', // Validasi setiap file dalam array (3MB)
        ]);
        
        // Update data student
        $student->fill($request->only([
            
            'nik',
            'tmpt_lhr',
            'tgl_lhr',
            'kelamin',
            'kebangsaan',
            'no_tlp_rmh',
            'no_tlp_kntr',
            
            'kualifikasi_pendidikan',
        ]));
        $user->fill($request->only(['no_tlp_hp','name',]));
        // dd($user);
        // Tangani file jika ada upload baru
        foreach (['foto_ktp', 'foto_ktm', 'pas_foto'] as $fileField) {
            if ($request->hasFile($fileField)) {
                // Cek jika file sebelumnya ada (tidak null atau kosong)
                if ($student->$fileField && Storage::disk('public')->exists($student->$fileField)) {
                    // Hapus file lama jika ada
                    Storage::disk('public')->delete($student->$fileField);
                }
                // Simpan file baru
                $fileData = FileHelper::storeFileWithUniqueName($request->file($fileField), $fileField); // $fileField sebagai baseDirectory
                $student->$fileField = $fileData['path'];
            }
        }
        if ($request->hasFile('kartu_hasil_studi')) {
            $oldKhsFiles = Studentattachmentfile::where('student_id', $student->id)
                ->where('type', 'kartu_hasil_studi')
                ->get();

            foreach ($oldKhsFiles as $oldFile) {
                Storage::disk('public')->delete($oldFile->path_file);
                $oldFile->delete();
            }

            // 2. Simpan SEMUA file KHS yang baru diunggah
            foreach ($request->file('kartu_hasil_studi') as $file) {
                $fileData = FileHelper::storeFileWithUniqueName($file, "student_attachments");
                Studentattachmentfile::create([
                    'student_id' => $student->id,
                    'type' => 'kartu_hasil_studi',
                    'path_file' => $fileData['path'],
                ]);
            }
        }

        if ($student->isDirty()) {
            $student->save();
        }
        if ($user->isDirty()) {
            $user->save();
        }

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    // untuk menghapus akun
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
