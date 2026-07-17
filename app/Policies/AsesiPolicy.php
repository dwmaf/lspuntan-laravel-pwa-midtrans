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
        if ($user->id === $asesi->mahasiswa->user_id) {
            return true;
        }

        // Admin bisa lihat semua
        if ($user->hasRole('admin')) {
            return true;
        }

        // Asesor hanya bisa lihat asesi di sertifikasi yang dia tangani
        return $user->hasRole('asesor') &&
            $asesi->sertifikasi->asesor()->where('user_id', $user->id)->exists();
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
        if ($user->id === $asesi->mahasiswa->user_id) {
            return true;
        }

        // Admin bisa update data asesi
        if ($user->hasRole('admin')) {
            return true;
        }

        // Jika user adalah asesor, periksa apakah dia adalah asesor penguji asesi ini
        if ($user->hasRole('asesor')) {
            $asesor = $user->asesor; // relasi user ke asesor
            return $asesor && $asesor->id === $asesi->asesor_id;
        }

        return false;
    }

    public function updateStatusFinal(User $user, Asesi $asesi): bool
    {
        // Pastikan user adalah asesor
        if (!$user->hasRole('asesor')) {
            return false;
        }

        // Asesor hanya bisa jika ia adalah asesor penguji asesi ini
        $asesor = $user->asesor;
        return $asesor && $asesor->id === $asesi->asesor_id;
    }

    public function assignAsesor(User $user, Asesi $asesi): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * belum terpakai
     */
    public function delete(User $user, Asesi $asesi): bool
    {
        // Hanya asesi pemilik data yang bisa membatalkan pendaftaran
        // Atau admin untuk kebutuhan audit/pembersihan
        return $user->id === $asesi->mahasiswa->user_id || $user->hasRole('admin');
    }

    /**
     * belum terpakai
     */
    public function restore(User $user, Asesi $asesi): bool
    {
        return false;
    }

    /**
     * belum terpakai
     */
    public function forceDelete(User $user, Asesi $asesi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can manage (create/update/delete) certificate for asesi.
     * Only admin can manage certificates, asesor cannot.
     */
    public function manageCertificate(User $user, Asesi $asesi): bool
    {
        // Hanya admin yang bisa manage sertifikat
        return $user->hasRole('admin');
    }

    public function downloadFile(User $user, Asesi $asesi)
    {
        if ($user->hasRole('admin'))
            return true;
        if ($user->hasRole('asesi') && $user->id === $asesi->mahasiswa->user_id)
            return true;

        if ($user->hasRole('asesor')) {
            $asesor = $user->asesor;
            return $asesor && $asesor->id === $asesi->asesor_id;
        }
        return false;
    }
}
