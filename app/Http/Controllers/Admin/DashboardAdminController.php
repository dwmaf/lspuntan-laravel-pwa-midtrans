<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skema;
use App\Models\Asesi;
use App\Models\Asesor;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Sertifikasi;
use Inertia\Inertia;

class DashboardAdminController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $hasAdminRole = $user->hasRole('admin');
        $isOnlyAsesor = $user->hasRole('asesor') && !$hasAdminRole;
        
        // Jika user hanya asesor, ambil ID asesor-nya
        $asesorId = null;
        if ($isOnlyAsesor) {
            $asesor = Asesor::where('user_id', $user->id)->first();
            $asesorId = $asesor?->id;
        }

        // Query sertifikasi berlangsung (filter untuk asesor)
        $sertifikasiBerlangsung = Sertifikasi::with('skema')
            ->withCount('asesi')
            ->where('status', 'berlangsung')
            ->when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
                $query->whereHas('asesor', function ($subQuery) use ($asesorId) {
                    $subQuery->where('asesor.id', $asesorId);
                });
            })
            ->get();

        // Hitung total asesi (filter untuk asesor)
        $totalAsesiCount = Asesi::when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
            $query->where('asesor_id', $asesorId);
        })->count();

        // Hitung asesi lulus (filter untuk asesor)
        $asesiLulusCount = Asesi::where('status_final', 'kompeten')
            ->when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
                $query->where('asesor_id', $asesorId);
            })
            ->count();

        // Base Query untuk asesi yang sedang dalam sertifikasi berlangsung (filter untuk asesor)
        $baseQuery = Asesi::whereHas('sertifikasi', function ($query) {
            $query->where('status', 'berlangsung');
        })
            ->when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
                $query->where('asesor_id', $asesorId);
            });

        $pipelineStats = [
            'verifikasi_berkas'            => (clone $baseQuery)->where('status_berkas', 'menunggu_verifikasi_admin')->count(),
            'revisi_asesi'                 => (clone $baseQuery)->where('status_berkas', 'perlu_perbaikan_berkas')->count(),
            'berkas_lengkap_belum_asesor'  => (clone $baseQuery)->where('status_berkas', 'sudah_lengkap')->whereNull('asesor_id')->count(),
            'ada_asesor_belum_ditetapkan'  => (clone $baseQuery)->whereNotNull('asesor_id')->where('status_final', 'belum_ditetapkan')->count(),
        ];

        $sertifikasiSelesaiCount = Sertifikasi::where('status', 'selesai')
            ->when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
                $query->whereHas('asesor', function ($subQuery) use ($asesorId) {
                    $subQuery->where('asesor.id', $asesorId);
                });
            })
            ->count();

        $recentActivities = Activity::with('causer')
            ->latest()
            ->take(5)
            ->get();

        $recentActivities->loadMissing('subject');
        $recentActivities->loadMorph('subject', [
            Asesor::class => ['user'],
        ]);

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
            $topSchemes = DB::table('asesi')
                ->join('sertifikasi', 'asesi.sertifikasi_id', '=', 'sertifikasi.id')
                ->join('skema', 'sertifikasi.skema_id', '=', 'skema.id')
                ->select('skema.nama_skema', DB::raw('count(asesi.id) as total_pendaftar'))
                ->groupBy('skema.id', 'skema.nama_skema')
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
            'sertifikasiBerlangsung' => $sertifikasiBerlangsung,
            'sertifikasiSelesaiCount' => $sertifikasiSelesaiCount, 
            'totalAsesiCount' => $totalAsesiCount, 
            'asesiLulusCount' => $asesiLulusCount, 
            'pipelineStats' => $pipelineStats,
            'charts' => $charts,
            'recentActivities' => $recentActivities,
            'isAsesor' => $isOnlyAsesor,
        ]);
    }

}
