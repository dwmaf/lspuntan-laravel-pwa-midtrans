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
                'pendingRegistrants' => [], // Reusing prop name from admin for consistency or clarity
                'pendingPayments' => [],
            ]);
        }

        $asesis = Asesi::with(['sertification.skema', 'sertification.asesors.user', 'transaction'])
            ->where('student_id', $student->id)
            ->get();

        $sertifikasiBerlangsung = $asesis->filter(function ($asesi) {
            return !in_array($asesi->status, ['lulus', 'gagal', 'ditolak']);
        })->values();

        $sertifikasiSelesai = $asesis->filter(function ($asesi) {
            return in_array($asesi->status, ['lulus', 'gagal']);
        })->values();
        
        // Pending payments: Transactions where status is pending
        // We can check the latest transaction for each asesi
        $pendingPayments = $asesis->filter(function ($asesi) {
             $latestTransaction = $asesi->transaction->sortByDesc('created_at')->first();
             return $latestTransaction && $latestTransaction->status === 'pending';
        })->values();


        return Inertia::render('Asesi/DashboardAsesi', [
            'sertifikasiBerlangsung' => $sertifikasiBerlangsung,
            'sertifikasiSelesai' => $sertifikasiSelesai,
            'pendingPayments' => $pendingPayments,
            'user' => $user,
            'student' => $student
        ]);
    }
}
