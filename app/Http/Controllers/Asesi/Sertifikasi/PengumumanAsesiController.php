<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\NewsRead;
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
        $sertification = Sertification::with(['news' => function($q) use ($request) {
            $q->with(['user', 'newsfiles', 'reads' => function($r) use ($request) {
                $r->where('user_id', $request->user()->id);
            }]);
        }])->findOrFail($sert_id);
        $asesi = Asesi::with(['student','transaction' => fn($q) => $q->latest()])->findOrFail($asesi_id);
        $asesi->latest_transaction = $asesi->transaction->first();
        return Inertia::render('Asesi/PengumumanAsesi', [
            'pengumumans' => $sertification->news->map(function($news) {
                $news->is_read = $news->reads->isNotEmpty();
                unset($news->reads); // Clean up sensitive/unnecessary data
                return $news;
            }),
            'sertification' => $sertification,
            'asesi' => $asesi,
            'initialNewsId' => $request->query('news_id'),
        ]);
    }

    public function mark_as_read($sert_id, $asesi_id, $news_id, Request $request)
    {
        $user_id = $request->user()->id;
        NewsRead::firstOrCreate([
            'news_id' => $news_id,
            'user_id' => $user_id
        ], [
            'read_at' => now()
        ]);
        
        return response()->json(['status' => 'success']);
    }

}
