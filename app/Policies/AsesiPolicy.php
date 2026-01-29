<?php

namespace App\Policies;

use App\Models\Asesi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AsesiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Asesi $asesi): bool
    {
        // Asesi hanya bisa lihat datanya sendiri
        if ($user->id === $asesi->student->user_id) {
            return true;
        }

        // Admin bisa lihat semua
        if ($user->hasRole('admin')) {
            return true;
        }

        // Asesor hanya bisa lihat asesi di sertifikasi yang dia tangani
        return $user->hasRole('asesor') && 
               $asesi->sertification->asesors()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya user dengan role asesi yang bisa mendaftar (membuat record asesi)
        return $user->hasRole('asesi');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Asesi $asesi): bool
    {
        // Asesi bisa update datanya sendiri
        if ($user->id === $asesi->student->user_id) {
            return true;
        }

        // Admin bisa update data asesi
        if ($user->hasRole('admin')) {
            return true;
        }

        // Asesor hanya bisa update asesi di sertifikasi yang dia tangani
        return $user->hasRole('asesor') && 
               $asesi->sertification->asesors()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Asesi $asesi): bool
    {
        // Hanya asesi pemilik data yang bisa membatalkan pendaftaran
        // Atau admin untuk kebutuhan audit/pembersihan
        return $user->id === $asesi->student->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Asesi $asesi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Asesi $asesi): bool
    {
        return false;
    }
}
