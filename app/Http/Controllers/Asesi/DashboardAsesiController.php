<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Asesi;
use App\Models\Pengumuman;
use App\Enums\StatusSertifikasi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardAsesiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $listAsesi = Asesi::with(['sertifikasi.skema', 'sertifikasi.asesor.user', 'asesor.user'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->get();

        $pengumumanTerbaru = collect();
        if ($listAsesi->isNotEmpty()) {
            $pengumumanTerbaru = Pengumuman::with('sertifikasi.skema')
                ->where(function ($query) use ($listAsesi) {
                    foreach ($listAsesi as $index => $asesi) {
                        $clause = function ($q) use ($asesi) {
                            $asesorUserId = $asesi->asesor?->user_id;
                            $q->where('sertifikasi_id', $asesi->sertifikasi_id)
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
                        'judul' => "Info: " . ($news->sertifikasi->skema->nama_skema ?? 'Umum'),
                        'pesan' => $news->content,
                        'tanggal' => $news->updated_at->diffForHumans(),
                        'tipe' => 'info',
                        'file' => $news->path_file
                    ];
                });
        }

        $sertifikasiBerlangsung = $listAsesi->filter(function ($asesi) {
            return $asesi->sertifikasi->status === StatusSertifikasi::BERLANGSUNG;
        })->values();

        $sertifikasiSelesai = $listAsesi->filter(function ($asesi) {
            return in_array($asesi->sertifikasi->status, [
                StatusSertifikasi::SELESAI,
                StatusSertifikasi::DIBATALKAN
            ]);
        })->values();

        return Inertia::render('Asesi/DashboardAsesi', [
            'sertifikasiBerlangsung' => $sertifikasiBerlangsung,
            'sertifikasiSelesai' => $sertifikasiSelesai,
            'pengumumanTerbaru' => $pengumumanTerbaru,
            'user' => $user,
            'mahasiswa' => $mahasiswa
        ]);
    }
}
