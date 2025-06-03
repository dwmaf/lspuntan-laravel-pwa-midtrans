<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
            'student' => $request->user()->student()
        ]);
    }
    public function edit_asesi(Request $request): View
    {
        return view('asesi.profile.edit', [
            'user' => $request->user(),
            'student' => $request->user()->student()
        ]);
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    public function update_asesi(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        // dd($student);

        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'tmpt_tgl_lhr' => 'required|string|max:255',
            'kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'kebangsaan' => 'required|string|max:255',
            'no_tlp_hp' => 'required|string|max:255',
            'kualifikasi_pendidikan' => 'required|string|max:255',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktm' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_khs' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pas_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update nama user jika berubah
        if ($user->name !== $request->name) {
            $user->name = $request->name;
            $user->save();
        }
        // Update data student
        $student->fill($request->only([
            'nik',
            'tmpt_tgl_lhr',
            'kelamin',
            'kebangsaan',
            'no_tlp_rmh',
            'no_tlp_kntr',
            'no_tlp_hp',
            'kualifikasi_pendidikan',
        ]));

        // Tangani file jika ada upload baru
        foreach (['foto_ktp', 'foto_ktm', 'foto_khs', 'pas_foto'] as $fileField) {
            if ($request->hasFile($fileField)) {
                // Cek jika file sebelumnya ada (tidak null atau kosong)
                if ($student->$fileField && Storage::disk('public')->exists($student->$fileField)) {
                    // Hapus file lama jika ada
                    Storage::disk('public')->delete($student->$fileField);
                }
                // Simpan file baru
                $student->$fileField = $request->file($fileField)->store($fileField, 'public');
            }
        }


        if ($student->isDirty()) {
            $student->save();
        }

        return back()->with('status', 'Profil berhasil diperbarui');
    }

    /**
     * Delete the user's account.
     */
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
