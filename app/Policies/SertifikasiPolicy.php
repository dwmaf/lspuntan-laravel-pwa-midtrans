<?php

namespace App\Policies;

use App\Models\Asesor;
use App\Models\Sertifikasi;
use App\Models\Sertifikat;
use App\Models\User;
use App\Models\Pengumuman;
use Illuminate\Auth\Access\Response;

class SertifikasiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin dan asesor bisa lihat list sertifikasi
        return $user->hasRole(['admin', 'asesor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sertifikasi $sertifikasi): bool
    {
        // Admin bisa lihat semua sertifikasi
        if ($user->hasRole('admin')) {
            return true;
        }

        // Asesor hanya bisa lihat sertifikasi yang mereka ampu
        if ($user->hasRole('asesor')) {
            $asesor = Asesor::where('user_id', $user->id)->first();

            if (!$asesor) {
                return false;
            }

            return $sertifikasi->asesor()->where('asesor.id', $asesor->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin yang bisa create sertifikasi baru
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sertifikasi $sertifikasi): bool
    {
        // Hanya admin yang bisa update sertifikasi
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sertifikasi $sertifikasi): bool
    {
        // Hanya admin yang bisa delete sertifikasi
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sertifikasi $Sertifikasi): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sertifikasi $Sertifikasi): bool
    {
        return $user->hasRole('admin');
    }

    public function manageAssessment(User $user, Sertifikasi $Sertifikasi): bool
    {
        // Hanya asesor yang bersangkutan dengan sertifikasi tersebut yang bisa akses
        if ($user->hasRole('asesor')) {
            $asesor = Asesor::where('user_id', $user->id)->first();

            if (!$asesor) {
                return false;
            }

            return $Sertifikasi->asesor()->where('asesor.id', $asesor->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can manage announcements for the certification.
     */
    public function manageAnnouncement(User $user, Sertifikasi $Sertifikasi): bool
    {
        return $this->view($user, $Sertifikasi);
    }
}
