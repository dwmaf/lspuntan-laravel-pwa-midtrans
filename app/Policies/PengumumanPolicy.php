<?php

namespace App\Policies;

use App\Models\Pengumuman;
use App\Models\User;
use App\Models\Asesi;
use Illuminate\Auth\Access\Response;

class PengumumanPolicy
{
    public function update(User $user, Pengumuman $pengumuman): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return $user->id === $pengumuman->user_id;
    }

    public function delete(User $user, Pengumuman $pengumuman): bool
    {
        return $this->update($user, $pengumuman);
    }

    public function downloadFile(User $user, Pengumuman $pengumuman)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('asesi')) {
            $mahasiswa = $user->mahasiswa;
            return $mahasiswa && Asesi::where('mahasiswa_id', $mahasiswa->id)
                ->where('sertifikasi_id', $pengumuman->sertifikasi_id)
                ->exists();
        }

        if ($user->hasRole('asesor')) {
            $asesor = $user->asesor;
            return $asesor && $asesor->sertifikasi()->where('sertifikasi.id', $pengumuman->sertifikasi_id)->exists();
        }

        return false;
    }
}
