<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skema;
use App\Models\Asesi;
use App\Models\Asesor;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;
use App\Models\Sertification;
use Inertia\Inertia;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $hasAdminRole = $user->hasRole('admin');
        $isOnlyAsesor = $user->hasRole('asesor') && !$hasAdminRole;
        
        // Jika user hanya asesor, ambil ID asesor-nya
        $asesorId = null;
        if ($isOnlyAsesor) {
            $asesor = Asesor::where('user_id', $user->id)->first();
            $asesorId = $asesor?->id;
        }

        // Query sertifikasi berlangsung (filter untuk asesor)
        $sertificationBerlangsung = Sertification::with('skema')
            ->withCount('asesis')
            ->where('status', 'berlangsung')
            ->when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
                $query->whereHas('asesors', function ($subQuery) use ($asesorId) {
                    $subQuery->where('asesors.id', $asesorId);
                });
            })
            ->get();

        // Hitung total asesi (filter untuk asesor)
        $totalAsesiCount = Asesi::when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
            $query->whereHas('sertification.asesors', function ($subQuery) use ($asesorId) {
                $subQuery->where('asesors.id', $asesorId);
            });
        })->count();

        // Hitung asesi lulus (filter untuk asesor)
        $asesiLulusCount = Asesi::where('status_final', 'kompeten')
            ->when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
                $query->whereHas('sertification.asesors', function ($subQuery) use ($asesorId) {
                    $subQuery->where('asesors.id', $asesorId);
                });
            })
            ->count();

        // Base Query untuk asesi yang sedang dalam sertifikasi berlangsung (filter untuk asesor)
        $baseQuery = Asesi::whereHas('sertification', function ($query) use ($isOnlyAsesor, $asesorId) {
            $query->where('status', 'berlangsung');
            if ($isOnlyAsesor && $asesorId) {
                $query->whereHas('asesors', function ($subQuery) use ($asesorId) {
                    $subQuery->where('asesors.id', $asesorId);
                });
            }
        });

        $pipelineStats = [
            'verifikasi_berkas' => (clone $baseQuery)->where('status_berkas', 'menunggu_verifikasi_admin')->count(),
            'revisi_asesi'      => (clone $baseQuery)->where('status_berkas', 'perlu_perbaikan_berkas')->count(),
            'menunggu_jadwal'   => (clone $baseQuery)->where('status_berkas', 'sudah_lengkap')
                                                   ->where('status_akses_asesmen', 'belum_diberikan')->count(),
            'proses_asesmen'    => (clone $baseQuery)->where('status_akses_asesmen', 'diberikan')
                                                   ->where('status_final', 'belum_ditetapkan')->count(),
        ];

        $sertificationSelesaiCount = Sertification::where('status', 'selesai')
            ->when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
                $query->whereHas('asesors', function ($subQuery) use ($asesorId) {
                    $subQuery->where('asesors.id', $asesorId);
                });
            })
            ->count();

        $recentActivities = Activity::with('causer')
            ->latest()
            ->take(5)
            ->get();

        // Charts hanya untuk admin, asesor tidak perlu
        $charts = null;
        if (!$isOnlyAsesor) {
            // Monthly stats
            $monthlyStats = Asesi::select(
                DB::raw('count(id) as count'), 
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as date")
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

            // Competency stats
            $competencyStats = Asesi::select('status_final', DB::raw('count(*) as count'))
                ->whereNotNull('status_final')
                ->groupBy('status_final')
                ->get();

            // Top schemes
            $topSchemes = DB::table('asesis')
                ->join('sertifications', 'asesis.sertification_id', '=', 'sertifications.id')
                ->join('skemas', 'sertifications.skema_id', '=', 'skemas.id')
                ->select('skemas.nama_skema', DB::raw('count(asesis.id) as total_pendaftar'))
                ->groupBy('skemas.id', 'skemas.nama_skema')
                ->orderByDesc('total_pendaftar')
                ->limit(5)
                ->get();

            $charts = [
                'monthlyStats' => $monthlyStats,
                'competencyStats' => $competencyStats,
                'topSchemes' => $topSchemes,
            ];
        }

        return Inertia::render('Admin/DashboardAdmin', [
            'sertificationBerlangsung' => $sertificationBerlangsung,
            'sertificationSelesaiCount' => $sertificationSelesaiCount, 
            'totalAsesiCount' => $totalAsesiCount, 
            'asesiLulusCount' => $asesiLulusCount, 
            'pipelineStats' => $pipelineStats,
            'charts' => $charts,
            'recentActivities' => $recentActivities,
            'isAsesor' => $isOnlyAsesor,
        ]);
    }

}
