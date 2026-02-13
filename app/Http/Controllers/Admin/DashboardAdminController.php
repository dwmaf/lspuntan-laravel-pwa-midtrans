<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skema;
use App\Models\Asesi;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;
use App\Models\Sertification;
use Inertia\Inertia;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $totalAsesiCount = Asesi::count();
        $asesiLulusCount = Asesi::where('status_final', 'kompeten')->count();

        // (Database Query lebih cepat daripada JS Filter)
        // Base Query untuk asesi yang sedang dalam sertifikasi berlangsung
        $baseQuery = Asesi::whereHas('sertification', function ($query) {
            $query->where('status', 'berlangsung');
        });

        $pipelineStats = [
            'verifikasi_berkas' => (clone $baseQuery)->where('status_berkas', 'menunggu_verifikasi_admin')->count(),
            'revisi_asesi'      => (clone $baseQuery)->where('status_berkas', 'perlu_perbaikan_berkas')->count(),
            'menunggu_jadwal'   => (clone $baseQuery)->where('status_berkas', 'sudah_lengkap')
                                                   ->where('status_akses_asesmen', 'belum_diberikan')->count(),
            'proses_asesmen'    => (clone $baseQuery)->where('status_akses_asesmen', 'diberikan')
                                                   ->where('status_final', 'belum_ditetapkan')->count(),
        ];

        $sertificationBerlangsung = Sertification::with('skema')->withCount('asesis')->where('status', 'berlangsung')->get();
        $sertificationSelesaiCount = Sertification::where('status', 'selesai')->count();
        $monthlyStats = Asesi::select(
            DB::raw('count(id) as count'), 
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as date")
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        $competencyStats = Asesi::select('status_final', DB::raw('count(*) as count'))
            ->whereNotNull('status_final')
            ->groupBy('status_final')
            ->get();

        $topSchemes = DB::table('asesis')
            ->join('sertifications', 'asesis.sertification_id', '=', 'sertifications.id')
            ->join('skemas', 'sertifications.skema_id', '=', 'skemas.id')
            ->select('skemas.nama_skema', DB::raw('count(asesis.id) as total_pendaftar'))
            ->groupBy('skemas.id', 'skemas.nama_skema')
            ->orderByDesc('total_pendaftar')
            ->limit(5)
            ->get();

        $recentActivities = Activity::with('causer')
            ->latest()
            ->take(5)
            ->get();
        return Inertia::render('Admin/DashboardAdmin', [
            'sertificationBerlangsung' => $sertificationBerlangsung,
            'sertificationSelesaiCount' => $sertificationSelesaiCount, 
            'totalAsesiCount' => $totalAsesiCount, 
            'asesiLulusCount' => $asesiLulusCount, 
            'pipelineStats' => $pipelineStats,
            'charts' => [
                'monthlyStats' => $monthlyStats,
                'competencyStats' => $competencyStats,
                'topSchemes' => $topSchemes,
            ],
            'recentActivities' => $recentActivities 
        ]);
    }

}
