<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Asesi;
use App\Models\News;
use App\Models\Sertification;
use App\Enums\StatusSertifikasi;
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
                'pengumumanTerbaru' => [],
            ]);
        }

        // Ambil ID sertifikasi yang diikuti oleh student ini
        $sertificationIds = Asesi::where('student_id', $student->id)
            ->pluck('sertification_id');

        $pengumumanTerbaru = News::with('sertification.skema')
            ->whereIn('sertification_id', $sertificationIds)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'judul' => "Info: " . ($news->sertification->skema->nama_skema ?? 'Umum'),
                    'pesan' => $news->content,
                    'tanggal' => $news->updated_at->diffForHumans(),
                    'tipe' => 'info',
                    'file' => $news->path_file
                ];
            });

        $asesis = Asesi::with(['sertification.skema', 'sertification.asesors.user'])
            ->where('student_id', $student->id)
            ->get();

        $sertifikasiBerlangsung = $asesis->filter(function ($asesi) {
            return $asesi->sertification->status === StatusSertifikasi::BERLANGSUNG; 
        })->values();

        $sertifikasiSelesai = $asesis->filter(function ($asesi) {
            return in_array($asesi->sertification->status, [
                StatusSertifikasi::SELESAI,
                StatusSertifikasi::DIBATALKAN
            ]);
        })->values();




        return Inertia::render('Asesi/DashboardAsesi', [
            'sertifikasiBerlangsung' => $sertifikasiBerlangsung,
            'sertifikasiSelesai' => $sertifikasiSelesai,
            'pengumumanTerbaru' => $pengumumanTerbaru,
            'user' => $user,
            'student' => $student
        ]);
    }
}
