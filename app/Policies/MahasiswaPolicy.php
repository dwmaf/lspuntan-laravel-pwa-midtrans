<?php

namespace App\Policies;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Asesi;
use Illuminate\Auth\Access\Response;

class MahasiswaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Mahasiswa $mahasiswa): bool
    {
        return $user->hasRole('admin') || $user->id === $mahasiswa->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Mahasiswa $mahasiswa): bool
    {
        return $user->hasRole('admin') || $user->id === $mahasiswa->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Mahasiswa $mahasiswa): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Mahasiswa $mahasiswa): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Mahasiswa $mahasiswa): bool
    {
        return $user->hasRole('admin');
    }

    public function downloadFile(User $user, Mahasiswa $mahasiswa)
    {
        if ($user->hasRole('admin'))
            return true;
        if ($user->hasRole('asesi') && $user->id === $mahasiswa->user_id)
            return true;

        if ($user->hasRole('asesor')) {
            $asesor = $user->asesor;
            return $asesor && Asesi::where('mahasiswa_id', $mahasiswa->id)
                ->where('asesor_id', $asesor->id)
                ->exists();
        }
        return false;
    }
}
