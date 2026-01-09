<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\News;
use App\Models\NewsRead;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use Inertia\Inertia;

class PengumumanAsesiController extends Controller
{
    public function index(Sertification $sertification, Asesi $asesi, Request $request)
    {
        // dd($request);
        NotificationController::markAsRead($request);
        $sertification->load(['news' => function($q) use ($request) {
            $q->with(['user', 'newsfiles', 'reads' => function($r) use ($request) {
                $r->where('user_id', $request->user()->id);
            }]);
        }, 'skema']);
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

    public function markAsRead(Sertification $sertification, Asesi $asesi, News $news, Request $request)
    {
        $user_id = $request->user()->id;
        NewsRead::firstOrCreate([
            'news_id' => $news->id,
            'user_id' => $user_id
        ], [
            'read_at' => now()
        ]);
        
        return response()->json(['status' => 'success']);
    }

}
