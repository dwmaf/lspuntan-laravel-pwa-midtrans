<?php

namespace App\Policies;

use App\Models\Asesmen;
use App\Models\User;
use App\Models\Asesi;
use Illuminate\Auth\Access\Response;

class AsesmenPolicy
{
    public function downloadFile(User $user, Asesmen $asesmen)
    {
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'asesi') {
            $student = $user->student;
            return $student && Asesi::where('student_id', $student->id)
                ->where('sertification_id', $asesmen->sertification_id)
                ->whereHas('asesor', function ($query) use ($asesmen) {
                    $query->where('user_id', $asesmen->user_id);
                })
                ->exists();
        }

        if ($user->role === 'asesor') {
            return $user->id === $asesmen->user_id;
        }

        return false;
    }
}
