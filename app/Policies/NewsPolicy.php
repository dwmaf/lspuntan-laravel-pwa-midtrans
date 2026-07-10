<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use App\Models\Asesi;
use Illuminate\Auth\Access\Response;

class NewsPolicy
{
    public function downloadFile(User $user, News $news)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('asesi')) {
            $student = $user->student;
            return $student && Asesi::where('student_id', $student->id)
                ->where('sertification_id', $news->sertification_id)
                ->exists();
        }

        if ($user->hasRole('asesor')) {
            $asesor = $user->asesor;
            return $asesor && $asesor->sertifications()->where('sertifications.id', $news->sertification_id)->exists();
        }

        return false;
    }
}
