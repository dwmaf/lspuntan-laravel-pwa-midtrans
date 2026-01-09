<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Helpers\FileHelper;
use App\Models\Asesi;

use Illuminate\Support\Facades\DB;
use App\Models\Sertification;
use Inertia\Inertia;

class DashboardAdminController extends Controller
{
    public function index()
    {
        // $pendingRegistrants = Transaction::
        //                         where('status','pending')
        //                         ->get();
        $totalAsesi = Asesi::all();
        $asesiBaruDaftar = Asesi::where('status_berkas','menunggu_verifikasi_berkas')->get();
        $asesiLulus = Asesi::where('status_final','kompeten')->get();
        $sertificationBerlangsung = Sertification::with('skema')->withCount('asesis')->where('status', 'berlangsung')->get();
        $sertificationSelesai = Sertification::with('skema')->withCount('asesis')->where('status', 'selesai')->get();
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

        return Inertia::render('Admin/DashboardAdmin', [
            // 'pendingRegistrants' => $pendingRegistrants,
            'sertificationBerlangsung' => $sertificationBerlangsung,
            'sertificationSelesai' => $sertificationSelesai,
            'totalAsesi' => $totalAsesi,
            'asesiLulus' => $asesiLulus,
            'asesiBaruDaftar' => $asesiBaruDaftar,
            'charts' => [
                'monthlyStats' => $monthlyStats,
                'competencyStats' => $competencyStats,
                'topSchemes' => $topSchemes,
            ]
        ]);
    }

}
