<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sertifikat;

class SertifikatPolicy
{
    public function downloadFile(User $user, Sertifikat $sertifikat)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('asesi')) {
            return $user->id === $sertifikat->asesi->student->user_id;
        }

        return false;
    }
}
