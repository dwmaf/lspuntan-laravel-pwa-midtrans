<?php

namespace App\Policies;

use App\Models\Asesor;
use App\Models\Sertification;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SertificationPolicy
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
    public function view(User $user, Sertification $sertification): bool
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

            return $sertification->asesors()->where('asesors.id', $asesor->id)->exists();
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
    public function update(User $user, Sertification $sertification): bool
    {
        // Hanya admin yang bisa update sertifikasi
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sertification $sertification): bool
    {
        // Hanya admin yang bisa delete sertifikasi
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sertification $sertification): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sertification $sertification): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage assessment (create/update tasks) for the certification.
     */
    public function manageAssessment(User $user, Sertification $sertification): bool
    {
        // Re-use view logic: Admin allowed, Asesor allowed if assigned
        return $this->view($user, $sertification);
    }

    /**
     * Determine whether the user can manage announcements for the certification.
     */
    public function manageAnnouncement(User $user, Sertification $sertification): bool
    {
        return $this->view($user, $sertification);
    }
}
