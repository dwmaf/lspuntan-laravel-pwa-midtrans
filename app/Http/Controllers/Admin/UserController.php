<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with('roles')
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->input('role'), function ($query, $role) {
                $query->whereHas('roles', fn($q) => $q->where('name', $role));
            })
            ->when($request->input('status'), function ($query, $status) {
                if ($status === 'banned') {
                    $query->whereNotNull('banned_at');
                } elseif ($status === 'active') {
                    $query->whereNull('banned_at');
                }
            })
            ->when($request->input('verified'), function ($query, $verified) {
                if ($verified === 'true') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($verified === 'false') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->latest()
            ->paginate(15)
            ->onEachSide(0)
            ->withQueryString();

        $roles = Role::all()->pluck('name');

        return Inertia::render('Admin/KelolaUser', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search','role','status','verified'])
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'no_tlp_hp' => 'nullable|string|max:20',
            'selectedRoles' => 'nullable|array',
            'selectedRoles.*' => 'string|exists:roles,name',
            'is_verified' => 'boolean',
        ]);

        $user->update($request->only('name','no_tlp_hp'));
        if ($request->has('selectedRoles')) {
            $user->syncRoles($request->selectedRoles);
        } else {
            $user->syncRoles([]);
        }

        if ($request->input('is_verified') && is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
            $user->save();
        }
        return redirect()->back()->with('message', 'User berhasil diperbarui.');
    }

    public function ban(User $user)
    {
        if ($user->id === Auth::id()){
            return redirect()->back()->withErrors(['error' => 'Anda tidak dapat menangguhkan akun Anda sendiri.']);
        }
        if ($user->banned_at) {
            $user->banned_at = null;
            $message = 'Akun pengguna berhasil diaktifkan kembali';
        } else {
            $user->banned_at = now();
            $message = 'Akun pengguna berhasil ditangguhkan.';
        }

        $user->save();
        return redirect()->back()->with('message', $message);
    }
}