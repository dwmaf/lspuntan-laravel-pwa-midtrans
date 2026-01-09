<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Asesi;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardAsesiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $student = $user->student;

        if (!$student) {
             return Inertia::render('Asesi/DashboardAsesi', [
                'sertifikasiBerlangsung' => [],
                'sertifikasiSelesai' => [],
                'pendingRegistrants' => [],
            ]);
        }

        $asesis = Asesi::with(['sertification.skema', 'sertification.asesors.user'])
            ->where('student_id', $student->id)
            ->get();

        $sertifikasiBerlangsung = $asesis->filter(function ($asesi) {
            return !in_array($asesi->status, ['lulus', 'gagal', 'ditolak']);
        })->values();

        $sertifikasiSelesai = $asesis->filter(function ($asesi) {
            return in_array($asesi->status, ['lulus', 'gagal']);
        })->values();
        
        


        return Inertia::render('Asesi/DashboardAsesi', [
            'sertifikasiBerlangsung' => $sertifikasiBerlangsung,
            'sertifikasiSelesai' => $sertifikasiSelesai,
            'user' => $user,
            'student' => $student
        ]);
    }
}
