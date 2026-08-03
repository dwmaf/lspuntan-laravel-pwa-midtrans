<?php

namespace App\Policies;

use App\Models\BerkasAsesi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BerkasAsesiPolicy
{
    public function downloadFile(User $user, BerkasAsesi $berkasAsesi)
    {
        if ($user->hasRole('admin'))
            return true;

        if ($user->hasRole('asesi') && $user->id === $berkasAsesi->asesi->mahasiswa->user_id)
            return true;

        if ($user->hasRole('asesor')) {
            $asesor = $user->asesor;
            return $asesor && $asesor->id === $berkasAsesi->asesi->asesor_id;
        }

        return false;
    }
}
