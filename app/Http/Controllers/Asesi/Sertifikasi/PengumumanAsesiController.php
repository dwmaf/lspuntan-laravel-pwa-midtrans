<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use Inertia\Inertia;

class PengumumanAsesiController extends Controller
{

    // buat nampilin daftar sertifikasi yg tersedia di sisi asesi
    public function index_pengumuman_asesi($sert_id, $asesi_id,  Request $request)
    {
        // dd($request);
        NotificationController::markAsRead($request);
        $sertification = Sertification::with('pengumumanasesmen.pembuatpengumuman.asesor','pengumumanasesmen.pengumumanasesmenfile')->findOrFail($sert_id);
        return Inertia::render('asesi.sertifikasi.pengumuman.index-pengumuman-asesi', [
            'pengumumans' => $sertification->pengumumanasesmen,
            'sertification' => $sertification,
            'asesi' => Asesi::with('student')->findOrFail($asesi_id)
        ]);
    }

}
