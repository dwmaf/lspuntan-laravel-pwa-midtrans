<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use Illuminate\Http\Request;
use App\Models\Sertifikasi;
use App\Models\Pengumuman;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class PengumumanAsesiController extends Controller
{
    public function index(Sertifikasi $sertifikasi, Asesi $asesi, Request $request)
    {
        Gate::authorize('view', $asesi);
        // dd($request);
        NotificationController::markAsRead($request);
        
        $sertifikasi->load('skema');
        $asesi->load('asesor');
        $asesorUserId = $asesi->asesor?->user_id;

        $listPengumuman = Pengumuman::where('sertifikasi_id', $sertifikasi->id)
            ->where(function ($query) use ($asesorUserId) {
                $query->whereDoesntHave('user.asesor')
                    ->when($asesorUserId, function ($q) use ($asesorUserId) {
                        $q->orWhere('user_id', $asesorUserId);
                    });
            })
            ->latest()
            ->get();

        return Inertia::render('Asesi/PengumumanAsesi', [
            'listPengumuman' => $listPengumuman,
            'sertifikasi' => $sertifikasi,
            'asesi' => $asesi,
            'initialPengumumanId' => $request->query('pengumuman_id'),
        ]);
    }

}
