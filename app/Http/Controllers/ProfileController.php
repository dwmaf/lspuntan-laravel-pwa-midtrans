<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\FileHelper;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{


    // buat nampilin halaman edit profile dari sisi admin
    public function edit(Request $request)
    {
        Gate::authorize('manageAdminProfile', User::class);
        /** @var \App\Models\User $user */
        $user = $request->user();
        if ($user->hasRole('asesor')) {
            $user->load('asesor.skema');
        }
        return Inertia::render('Admin/Profile/ProfileAdmin', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'isSubscribed' => !is_null($request->user()->fcm_token),
        ]);
    }
    // buat nampilin halaman edit profile dari sisi asesi
    public function edit_asesi(Request $request)
    {
        Gate::authorize('manageAsesiProfile', User::class);
        $user = $request->user()->load('mahasiswa');
        return Inertia::render('Asesi/Profile/ProfileAsesi', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
            'mahasiswa' => $user->mahasiswa,
            'isSubscribed' => !is_null($request->user()->fcm_token),
        ]);
    }
    // buat mengupdate profile yg tadi diedit dari sisi admin
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        Gate::authorize('manageAdminProfile', User::class);
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
        Gate::authorize('manageAsesiProfile', User::class);
        $mahasiswa = $request->user()->mahasiswa;
        $user = $mahasiswa->user;

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

        $mahasiswa->fill($request->only(['nik', 'tmpt_lhr', 'tgl_lhr', 'kelamin', 'kebangsaan', 'no_tlp_rmh', 'no_tlp_kntr', 'kualifikasi_pendidikan',]));
        $user->fill($request->only(['no_tlp_hp', 'name',]));
        // dd($user);
        FileHelper::handleSingleFileDeletes($mahasiswa, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($mahasiswa, ['pas_foto', 'foto_ktp'], $request, 'berkas_mahasiswa');
        FileHelper::saveIfDirty([$mahasiswa, $user]);

        return back()->with('message', 'Profil berhasil diperbarui');
    }
}
