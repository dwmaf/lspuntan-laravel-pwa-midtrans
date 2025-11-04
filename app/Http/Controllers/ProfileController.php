<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\Student;
use App\Models\Studentfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Helpers\FileHelper;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{


    // buat nampilin halaman edit profile dari sisi admin
    public function edit(Request $request)
    {
        return Inertia::render('Admin/Profile/ProfileAdmin', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }
    // buat nampilin halaman edit profile dari sisi asesi
    public function edit_asesi(Request $request)
    {
        $user = $request->user()->load('student');
        return Inertia::render('Asesi/Profile/ProfileAsesi', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
            'student' => $user->student
        ]);
    }
    // buat mengupdate profile yg tadi diedit dari sisi admin
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty(['email', 'no_tlp_hp'])) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return back()->with('message', 'Profil berhasil diperbaharui');
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
            'foto_ktp' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files', [])) && in_array('foto_ktp', $request->input('delete_files', []));
                }),
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],
            'pas_foto' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files', [])) && in_array('pas_foto', $request->input('delete_files', []));
                }),
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],
            'delete_files' => 'nullable|array',
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
        $user->fill($request->only(['no_tlp_hp', 'name',]));
        // dd($user);
        if ($request->has('delete_files')) {
            foreach ($request->delete_files as $fieldName) {
                if ($student->$fieldName) {
                    Storage::disk('public')->delete($student->$fieldName);
                    $student->$fieldName = null;
                }
            }
        }

        foreach (['foto_ktp', 'pas_foto'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $student->$fileField = FileHelper::storeFileWithUniqueName($request->file($fileField), 'student_files')['path'];
            }
        }

        if ($student->isDirty()) {
            $student->save();
        }
        if ($user->isDirty()) {
            $user->save();
        }

        return back()->with('message', 'Profil berhasil diperbarui');
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
