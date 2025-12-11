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
use App\Models\Transaction;
use App\Models\Sertification;
use Inertia\Inertia;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $pendingRegistrants = Transaction::
                                where('status','pending')
                                ->get();
        $totalAsesi = Asesi::all();
        $asesiBaruDaftar = Asesi::where('status','menunggu_verifikasi_berkas')->get();
        $asesiLulus = Asesi::where('status','lulus_sertifikasi')->get();
        $sertificationBerlangsung = Sertification::with('skema')->withCount('asesis')->where('status', 'berlangsung')->get();
        $sertificationSelesai = Sertification::with('skema')->withCount('asesis')->where('status', 'selesai')->get();
        return Inertia::render('Admin/DashboardAdmin', [
            'pendingRegistrants' => $pendingRegistrants,
            'sertificationBerlangsung' => $sertificationBerlangsung,
            'sertificationSelesai' => $sertificationSelesai,
            'totalAsesi' => $totalAsesi,
            'asesiLulus' => $asesiLulus,
            'asesiBaruDaftar' => $asesiBaruDaftar,
        ]);
    }

}
