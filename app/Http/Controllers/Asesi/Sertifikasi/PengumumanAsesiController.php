<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Inertia\Inertia;

class PengumumanAsesiController extends Controller
{
    public function index(Sertification $sertification, Asesi $asesi, Request $request)
    {
        // dd($request);
        NotificationController::markAsRead($request);
        $sertification->load(['news', 'skema']);
        return Inertia::render('Asesi/PengumumanAsesi', [
            'pengumumans' => $sertification->news,
            'sertification' => $sertification,
            'asesi' => $asesi,
            'initialNewsId' => $request->query('news_id'),
        ]);
    }

}
