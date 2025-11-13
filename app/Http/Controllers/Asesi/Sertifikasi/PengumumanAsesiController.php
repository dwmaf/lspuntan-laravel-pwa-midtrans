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
    public function index_pengumuman_asesi($sert_id, $asesi_id, Request $request)
    {
        // dd($request);
        NotificationController::markAsRead($request);
        $sertification = Sertification::with('news.user','news.newsfiles')->findOrFail($sert_id);
        $asesi = Asesi::with(['student','transaction' => fn($q) => $q->latest()])->findOrFail($asesi_id);
        $asesi->latest_transaction = $asesi->transaction->first();
        return Inertia::render('Asesi/PengumumanAsesi', [
            'pengumumans' => $sertification->news,
            'sertification' => $sertification,
            'asesi' => $asesi,
            'initialNewsId' => $request->query('news_id'),
        ]);
    }

}
