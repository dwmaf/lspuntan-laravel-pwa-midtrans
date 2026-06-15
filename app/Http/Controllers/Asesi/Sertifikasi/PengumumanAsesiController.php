<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\News;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class PengumumanAsesiController extends Controller
{
    public function index(Sertification $sertification, Asesi $asesi, Request $request)
    {
        Gate::authorize('view', $asesi);
        // dd($request);
        NotificationController::markAsRead($request);
        
        $sertification->load('skema');
        $asesi->load('asesor');
        $asesorUserId = $asesi->asesor?->user_id;

        $pengumumans = News::where('sertification_id', $sertification->id)
            ->where(function ($query) use ($asesorUserId) {
                $query->whereDoesntHave('user.asesor')
                    ->when($asesorUserId, function ($q) use ($asesorUserId) {
                        $q->orWhere('user_id', $asesorUserId);
                    });
            })
            ->latest()
            ->get();

        return Inertia::render('Asesi/PengumumanAsesi', [
            'pengumumans' => $pengumumans,
            'sertification' => $sertification,
            'asesi' => $asesi,
            'initialNewsId' => $request->query('news_id'),
        ]);
    }

}
