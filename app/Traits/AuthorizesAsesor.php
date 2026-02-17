<?php

namespace App\Traits;

use App\Models\Asesor;
use App\Models\Sertification;

trait AuthorizesAsesor
{
    /**
     * Check if asesor has access to this certification
     * Admin can access all certifications
     * Asesor can only access certifications they are assigned to
     */
    protected function authorizeAsesor(Sertification $sertification): void
    {
        $user = auth()->user();
        $hasAdminRole = $user->hasRole('admin');
        $isOnlyAsesor = $user->hasRole('asesor') && !$hasAdminRole;

        if ($isOnlyAsesor) {
            $asesor = Asesor::where('user_id', $user->id)->first();
            
            // Load asesors relation if not loaded
            if (!$sertification->relationLoaded('asesors')) {
                $sertification->load('asesors');
            }
            
            // Cek apakah asesor ini di-assign ke sertifikasi ini
            if (!$asesor || !$sertification->asesors->contains('id', $asesor->id)) {
                abort(403, 'Anda tidak memiliki akses ke sertifikasi ini.');
            }
        }
    }
}
