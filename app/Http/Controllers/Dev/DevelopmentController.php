<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Models\Asesi;
use App\Models\Asesmen;
use Illuminate\Support\Facades\DB;
use App\Models\Sertification;
use App\Models\Skema;
use App\Models\Asesor;
use App\Models\News;
use App\Models\User;
use App\Traits\SendsPushNotifications;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;

class DevelopmentController extends Controller
{
    use SendsPushNotifications;
    public function index(Request $request)
    {
        return Inertia::render('Dev/ResetData', [
            'sertifications' => Sertification::with('skema', 'asesors.user', 'paymentInstruction','asesmen')->withCount('asesis')->get(),
        ]);
    }

    public function detailSertification($sert_id)
    {
        return Inertia::render('Dev/ResetDataDetailSertif', [
            'sertification' => Sertification::with('skema', 'asesors.user', 'paymentInstruction','asesmen', 'news')->findOrFail($sert_id),
        ]);
    }

    public function storeDummyNews($sert_id, Request $request)
    {
        $newsData = [];
        $now = now();
        $total = 30;
        for ($i = 1; $i <= $total; $i++) {
            $timestamp = $now->copy()->subMinutes(($total - $i) * 2);
            $newsData[] = [
                'sertification_id' => $sert_id,
                'user_id' => $request->user()->id,
                'content' => "Ini dalah pengumuman uji coba ke-{$i} untuk menguji fitur infinite scrolling.",
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        News::insert($newsData);
        return redirect()->back()->with('message', '30 Pengumuman berhasil dibuat');
    }

    public function destroyNews($sert_id)
    {
        News::where('sertification_id', $sert_id)->delete();
        return redirect()->back()->with('message', 'Semua Pengumuman berhasil dihapus');
    }

    public function destroyAsesmen($sert_id)
    {
        Asesmen::where('sertification_id',$sert_id)->delete();
        return redirect()->back()->with('message', 'Data berhasil disimpan!');
    }

    public function listAsesis($sert_id)
    {
        return Inertia::render('Dev/ListAsesi', [
            'sertification' => Sertification::with('skema', 'asesors.user', 'asesis.student.user')->findOrFail($sert_id),
        ]);
    }

    
}
