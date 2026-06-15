<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Asesi;
use App\Models\News;
use App\Models\Sertification;
use App\Enums\StatusSertifikasi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardAsesiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $student = $user->student;

        // if (!$student) {
        //     return Inertia::render('Asesi/DashboardAsesi', [
        //         'sertifikasiBerlangsung' => [],
        //         'sertifikasiSelesai' => [],
        //         'pengumumanTerbaru' => [],
        //     ]);
        // }

        $asesis = Asesi::with(['sertification.skema', 'sertification.asesors.user', 'asesor.user'])
            ->where('student_id', $student->id)
            ->get();

        $pengumumanTerbaru = collect();
        if ($asesis->isNotEmpty()) {
            $pengumumanTerbaru = News::with('sertification.skema')
                ->where(function ($query) use ($asesis) {
                    foreach ($asesis as $index => $asesi) {
                        $clause = function ($q) use ($asesi) {
                            $asesorUserId = $asesi->asesor?->user_id;
                            $q->where('sertification_id', $asesi->sertification_id)
                              ->where(function ($sub) use ($asesorUserId) {
                                  $sub->whereDoesntHave('user.asesor')
                                      ->when($asesorUserId, function ($sub2) use ($asesorUserId) {
                                          $sub2->orWhere('user_id', $asesorUserId);
                                      });
                              });
                        };
                        if ($index === 0) {
                            $query->where($clause);
                        } else {
                            $query->orWhere($clause);
                        }
                    }
                })
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($news) {
                    return [
                        'id' => $news->id,
                        'judul' => "Info: " . ($news->sertification->skema->nama_skema ?? 'Umum'),
                        'pesan' => $news->content,
                        'tanggal' => $news->updated_at->diffForHumans(),
                        'tipe' => 'info',
                        'file' => $news->path_file
                    ];
                });
        }

        $sertifikasiBerlangsung = $asesis->filter(function ($asesi) {
            return $asesi->sertification->status === StatusSertifikasi::BERLANGSUNG;
        })->values();

        $sertifikasiSelesai = $asesis->filter(function ($asesi) {
            return in_array($asesi->sertification->status, [
                StatusSertifikasi::SELESAI,
                StatusSertifikasi::DIBATALKAN
            ]);
        })->values();

        return Inertia::render('Asesi/DashboardAsesi', [
            'sertifikasiBerlangsung' => $sertifikasiBerlangsung,
            'sertifikasiSelesai' => $sertifikasiSelesai,
            'pengumumanTerbaru' => $pengumumanTerbaru,
            'user' => $user,
            'student' => $student
        ]);
    }
}
