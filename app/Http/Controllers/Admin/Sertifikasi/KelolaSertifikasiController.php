<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Exports\LaporanSertifikasiExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Sertifikasi;
use App\Models\Asesi;
use App\Models\Skema;
use App\Models\Asesor;
use App\Models\User;
use App\Models\Pengumuman;
use App\Models\Asesmen;
use App\Models\Sertifikat;
use Inertia\Inertia;
use App\Traits\SendsPushNotifications;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

class KelolaSertifikasiController extends Controller
{
    use SendsPushNotifications;
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $hasAdminRole = $user->hasRole('admin');
        $isOnlyAsesor = $user->hasRole('asesor') && !$hasAdminRole;
        $asesorId = null;

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ], [
            'date_from.date' => 'Format tanggal awal tidak valid.',
            'date_to.date' => 'Format tanggal akhir tidak valid.',
            'date_to.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.',
        ]);

        // Jika user HANYA asesor (bukan admin), ambil ID asesor-nya
        if ($isOnlyAsesor) {
            $asesor = Asesor::where('user_id', $user->id)->first();
            $asesorId = $asesor?->id;
        }

        // Query untuk sertifikasi berlangsung
        $sertifikasiBerlangsung = Sertifikasi::with('skema', 'asesor.user')
            ->withCount('asesi')
            ->where('status', 'berlangsung')
            ->when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
                $query->withCount(['asesi as asesi_asesor_count' => function ($q) use ($asesorId) {
                    $q->where('asesor_id', $asesorId);
                }])->whereHas('asesor', function ($subQuery) use ($asesorId) {
                    $subQuery->where('asesor.id', $asesorId);
                });
            })
            ->orderBy('tgl_apply_dibuka', 'desc')
            ->get();

        // Query untuk sertifikasi selesai
        $sertifikasiSelesai = Sertifikasi::with('skema', 'asesor.user')
            ->withCount('asesi')
            ->when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
                $query->withCount(['asesi as asesi_asesor_count' => function ($q) use ($asesorId) {
                    $q->where('asesor_id', $asesorId);
                }]);
            })
            ->when($request->input('date_from'), function ($query, $dateFrom) {
                $query->whereDate('tgl_apply_dibuka', '>=', $dateFrom);
            })
            ->when($request->input('date_to'), function ($query, $dateTo) {
                $query->whereDate('tgl_apply_ditutup', '<=', $dateTo);
            })
            ->when($request->input('asesor'), function ($query, $asesorId) {
                $query->whereHas('asesor', function ($subQuery) use ($asesorId) {
                    $subQuery->where('asesor.id', $asesorId);
                });
            })
            ->when($request->input('skema'), function ($query, $skema) {
                $query->whereHas('skema', fn($q) => $q->where('id', $skema));
            })
            ->when($isOnlyAsesor && $asesorId, function ($query) use ($asesorId) {
                $query->whereHas('asesor', function ($subQuery) use ($asesorId) {
                    $subQuery->where('asesor.id', $asesorId);
                });
            })
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->orderBy('tgl_apply_ditutup', 'desc')
            ->latest()
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        return Inertia::render('Admin/KelolaSertifikasiAdmin', [
            'sertifikasi_berlangsung' => $sertifikasiBerlangsung,
            'sertifikasi_selesai' => $sertifikasiSelesai,
            'listAsesor' => Asesor::with('skema', 'user')->withCount('sertifikasi')->get(),
            'listSkema' => Skema::all(),
            'activeSkema' => Skema::where('is_active', true)->get(),
            'filters' => $request->only(['date_from', 'date_to', 'asesor', 'skema', 'tab']),
            'isAsesor' => $isOnlyAsesor,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'skema_id' => 'required',
            'asesor_ids' => 'required|array',
            'asesor_ids.*' => 'exists:asesor,id',
            'tgl_apply_dibuka' => 'required|date',
            'tgl_apply_ditutup' => 'required|date|after_or_equal:tgl_apply_dibuka',
            'tgl_asesmen_mulai' => 'nullable|date',
            'tgl_asesmen_selesai' => 'nullable|date|after_or_equal:tgl_asesmen_mulai',
            'tuk' => 'nullable|string|max:255',
            'biaya' => 'required|numeric|min:0',
            'no_rek' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'atas_nama_rek' => 'required|string|max:255',
        ]);

        $sertifikasi = null;

        DB::transaction(function () use ($validatedData, &$sertifikasi) {
            $sertifikasi = Sertifikasi::create([
                'skema_id' => $validatedData['skema_id'],
                'tgl_apply_dibuka' => $validatedData['tgl_apply_dibuka'],
                'tgl_apply_ditutup' => $validatedData['tgl_apply_ditutup'],
                'tgl_asesmen_mulai' => $validatedData['tgl_asesmen_mulai'] ?? null,
                'tgl_asesmen_selesai' => $validatedData['tgl_asesmen_selesai'] ?? null,
                'biaya' => $validatedData['biaya'],
                'no_rek' => $validatedData['no_rek'],
                'bank' => $validatedData['bank'],
                'atas_nama_rek' => $validatedData['atas_nama_rek'],
                'tuk' => $validatedData['tuk'] ?? null,
                'status' => 'berlangsung',
            ]);

            if (!empty($validatedData['asesor_ids'])) {
                $sertifikasi->asesor()->attach($validatedData['asesor_ids']);
            }
        });

        if ($sertifikasi) {
            // Simpan ID asesor + nama ke activity log created (biar ga stale)
            $asesorIds = $validatedData['asesor_ids'] ?? [];
            if (!empty($asesorIds)) {
                $activity = Activity::where('subject_type', Sertifikasi::class)
                    ->where('subject_id', $sertifikasi->id)
                    ->where('event', 'created')
                    ->latest()
                    ->first();
                if ($activity) {
                    $asesorNames = Asesor::whereIn('id', $asesorIds)->with('user')->get()->pluck('user.name')->toArray();
                    $properties = $activity->properties;
                    $attributes = $properties->get('attributes', []);
                    $attributes['asesor'] = $asesorIds;
                    $attributes['asesor_names'] = $asesorNames;
                    $properties->put('attributes', $attributes);
                    $activity->properties = $properties;
                    $activity->save();
                }
            }

            $asesiRecipients = User::role('asesi')->get();
            if ($asesiRecipients->isNotEmpty()) {
                $title = 'Sertifikasi Baru Dibuka!';
                $body = "Sertifikasi baru untuk '{$sertifikasi->skema->nama_skema}' telah dibuka. Cek sekarang!";
                $url = route('asesi.sertifikasi.index');
                $this->sendMulticastNotification($asesiRecipients, $title, $body, $url, 'SertifikasiBaru');
            }

            $asesorUsers = $sertifikasi->asesor->map->user;
            if ($asesorUsers->isNotEmpty()) {
                $title = 'Penugasan Sertifikasi Baru!';
                $body = "Anda ditugaskan sebagai asesor untuk sertifikasi '{$sertifikasi->skema->nama_skema}'. Cek sekarang!";
                $url = route('admin.kelolasertifikasi.show', $sertifikasi);
                $this->sendMulticastNotification($asesorUsers, $title, $body, $url, 'PenugasanSertifikasi');
            }
        }
        return redirect(route('admin.kelolasertifikasi.show', $sertifikasi))->with('message', 'Sertifikasi berhasil dimulai!');
    }

    public function show(Sertifikasi $sertifikasi)
    {
        Gate::authorize('view', $sertifikasi);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $hasAdminRole = $user->hasRole('admin');
        $isOnlyAsesor = $user->hasRole('asesor') && !$hasAdminRole;

        $asesor = null;
        if ($isOnlyAsesor) {
            $asesor = Asesor::where('user_id', $user->id)->first();
        }

        $sertifikasi->load('skema', 'asesor.user')->loadCount([
            'asesi',
            'asesi as asesi_menunggu_verifikasi_count' => function ($query) {
                $query->where('status_berkas', 'menunggu_verifikasi_admin');
            },
            'asesi as asesi_perlu_perbaikan_count' => function ($query) {
                $query->where('status_berkas', 'perlu_perbaikan_berkas');
            },
            'asesi as asesi_berkas_lengkap_count' => function ($query) {
                $query->where('status_berkas', 'sudah_lengkap')->whereNull('asesor_id');
            },
            'asesi as asesi_proses_asesmen_count' => function ($query) {
                $query->whereNotNull('asesor_id')->where('status_final', 'belum_ditetapkan');
            },
            'asesi as asesi_kompeten_count' => function ($query) {
                $query->where('status_final', 'kompeten');
            },
            'asesi as asesi_kompeten_belum_sertifikat_count' => function ($query) {
                $query->where('status_final', 'kompeten')->whereDoesntHave('sertifikat');
            },
            'asesi as asesi_belum_kompeten_count' => function ($query) {
                $query->where('status_final', 'belum_kompeten');
            },
            'asesi as asesi_diskualifikasi_count' => function ($query) {
                $query->where('status_final', 'diskualifikasi');
            },
            ...($asesor ? [
                'asesi as asesi_asesor_count' => function ($query) use ($asesor) {
                    $query->where('asesor_id', $asesor->id);
                },
                'asesi as asesi_asesor_kompeten_count' => function ($query) use ($asesor) {
                    $query->where('asesor_id', $asesor->id)->where('status_final', 'kompeten');
                },
                'asesi as asesi_asesor_kompeten_belum_sertifikat_count' => function ($query) use ($asesor) {
                    $query->where('asesor_id', $asesor->id)->where('status_final', 'kompeten')->whereDoesntHave('sertifikat');
                },
                'asesi as asesi_asesor_belum_kompeten_count' => function ($query) use ($asesor) {
                    $query->where('asesor_id', $asesor->id)->where('status_final', 'belum_kompeten');
                },
                'asesi as asesi_asesor_diskualifikasi_count' => function ($query) use ($asesor) {
                    $query->where('asesor_id', $asesor->id)->where('status_final', 'diskualifikasi');
                },
                'asesi as asesi_asesor_belum_ditetapkan_count' => function ($query) use ($asesor) {
                    $query->where('asesor_id', $asesor->id)->where('status_final', 'belum_ditetapkan');
                },
            ] : []),
        ]);
        return Inertia::render('Admin/DetailSertifikasiAdmin', [
            'sertifikasi' => $sertifikasi,
            'listAsesor' => Asesor::with('skema', 'user')->get(),
            'listSkema' => Skema::all(),
            'activeSkemas' => Skema::where('is_active', true)->get(),
            'isAsesor' => $isOnlyAsesor,
        ]);
    }

    public function update(Sertifikasi $sertifikasi, Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'asesor_ids' => 'required|array',
            'asesor_ids.*' => 'exists:asesor,id',
            'tgl_apply_dibuka' => 'required|date',
            'tgl_apply_ditutup' => 'required|date|after_or_equal:tgl_apply_dibuka',
            'tgl_asesmen_mulai' => 'nullable|date',
            'tgl_asesmen_selesai' => 'nullable|date|after_or_equal:tgl_asesmen_mulai',
            'biaya' => 'required|numeric|min:0',
            'tuk' => 'nullable|string|max:255',
            'no_rek' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'atas_nama_rek' => 'required|string|max:255',
            'status' => 'required|in:berlangsung,selesai,dibatalkan',
        ]);

        $sertifikasi->load('asesor.user', 'asesi', 'skema');

        $oldAsesorIds = $sertifikasi->asesor->pluck('id')->toArray();

        $removedAsesors = $sertifikasi->asesor->whereNotIn('id', $validatedData['asesor_ids'] ?? []);

        $blockedAsesorNames = [];
        foreach ($removedAsesors as $asesor) {
            $hasAssigned = $sertifikasi->asesi->contains('asesor_id', $asesor->id);
            if ($hasAssigned) {
                $blockedAsesorNames[] = $asesor->user->name;
            }
        }

        if (!empty($blockedAsesorNames)) {
            $names = implode(', ', $blockedAsesorNames);
            return back()->withErrors([
                'asesor_ids' => "Asesor {$names} tidak bisa dihapus dari sertifikasi {$sertifikasi->skema->nama_skema}, karena sudah diassign ke asesi."
            ]);
        }

        DB::transaction(function () use ($validatedData, $sertifikasi, $request) {
            $sertifikasi->update([
                'tgl_apply_dibuka' => $validatedData['tgl_apply_dibuka'],
                'tgl_apply_ditutup' => $validatedData['tgl_apply_ditutup'],
                'tgl_asesmen_mulai' => $validatedData['tgl_asesmen_mulai'] ?? null,
                'tgl_asesmen_selesai' => $validatedData['tgl_asesmen_selesai'] ?? null,
                'biaya' => $validatedData['biaya'],
                'no_rek' => $validatedData['no_rek'],
                'bank' => $validatedData['bank'],
                'atas_nama_rek' => $validatedData['atas_nama_rek'],
                'tuk' => $validatedData['tuk'] ?? null,
                'status' => $validatedData['status'],
            ]);
            if (isset($validatedData['asesor_ids'])) {
                $sertifikasi->asesor()->sync($validatedData['asesor_ids']);
            } else {
                $sertifikasi->asesor()->sync([]);
            }
        });

        // Log asesor changes manually (pivot sync tidak otomatis di-log)
        $sertifikasi->load('asesor.user');
        $newAsesorIds = $sertifikasi->asesor->pluck('id')->toArray();
        $addedIds = array_diff($newAsesorIds, $oldAsesorIds);
        $removedIds = array_diff($oldAsesorIds, $newAsesorIds);

        if (!empty($addedIds) || !empty($removedIds)) {
            $properties = [];
            $parts = [];
            if (!empty($addedIds)) {
                $names = Asesor::whereIn('id', $addedIds)->with('user')->get()->pluck('user.name')->toArray();
                $properties['added_asesor_ids'] = $addedIds;
                $properties['added_asesor_names'] = $names;
                $parts[] = 'menambahkan asesor ' . implode(', ', $names);
            }
            if (!empty($removedIds)) {
                $names = Asesor::whereIn('id', $removedIds)->with('user')->get()->pluck('user.name')->toArray();
                $properties['removed_asesor_ids'] = $removedIds;
                $properties['removed_asesor_names'] = $names;
                $parts[] = 'menghapus asesor ' . implode(', ', $names);
            }

            activity('Sertifikasi')
                ->performedOn($sertifikasi)
                ->causedBy(Auth::user())
                ->withProperties($properties)
                ->tap(function ($activity) use ($parts) {
                    $activity->description = 'Asesor sertifikasi diubah: ' . implode('; ', $parts);
                })
                ->log('updated');
        }

        return redirect()->back()->with('message', 'Data Sertifikasi berhasil diupdate');
    }

    public function export_excel(Sertifikasi $sertifikasi)
    {
        $sertifikasi->load('skema', 'asesi.mahasiswa.user', 'asesor.user');
        $fileName = 'Laporan_Sertifikasi_' . Str::slug($sertifikasi->skema->nama_skema) . '.xlsx';
        return Excel::download(new LaporanSertifikasiExport($sertifikasi), $fileName);
    }

    public function indexLog(Sertifikasi $sertifikasi, Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ], [
            'date_from.date' => 'Format tanggal awal tidak valid.',
            'date_to.date' => 'Format tanggal akhir tidak valid.',
            'date_to.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.',
        ]);

        $sertifikasi->load('skema', 'asesi.mahasiswa.user', 'asesor.user');

        $existingNewsIds = $sertifikasi->pengumuman()->pluck('id');
        $deletedNewsIds = Activity::where('subject_type', Pengumuman::class)
            ->where('properties->attributes->sertifikasi_id', $sertifikasi->id)
            ->where('event', 'created')
            ->pluck('subject_id');
        $allNewsIds = $existingNewsIds->merge($deletedNewsIds)->unique()->values();

        $existingAsesmenIds = $sertifikasi->asesmen()->pluck('id');
        $deletedAsesmenIds = Activity::where('subject_type', Asesmen::class)
            ->where('properties->attributes->sertifikasi_id', $sertifikasi->id)
            ->where('event', 'created')
            ->pluck('subject_id');
        $allAsesmenIds = $existingAsesmenIds->merge($deletedAsesmenIds)->unique()->values();

        $allAsesisIds = $sertifikasi->asesi()->pluck('id');

        $existingSertifikatIds = Sertifikat::whereIn('asesi_id', $allAsesisIds)->pluck('id');
        $deletedSertifikatIds = Activity::where('subject_type', Sertifikat::class)
            ->whereIn('properties->attributes->asesi_id', $allAsesisIds)
            ->where('event', 'created')
            ->pluck('subject_id');
        $allSertifikatIds = $existingSertifikatIds->merge($deletedSertifikatIds)->unique()->values();

        // Map sertifikat_id => asesi_id untuk semua sertifikat (existing + deleted)
        $sertifikatAsesiMap = [];
        Sertifikat::whereIn('id', $allSertifikatIds)->get()->each(function ($s) use (&$sertifikatAsesiMap) {
            $sertifikatAsesiMap[$s->id] = $s->asesi_id;
        });
        Activity::where('subject_type', Sertifikat::class)
            ->whereIn('subject_id', $allSertifikatIds)
            ->where('event', 'created')
            ->get()
            ->each(function ($a) use (&$sertifikatAsesiMap) {
                if (!isset($sertifikatAsesiMap[$a->subject_id])) {
                    $props = is_string($a->properties) ? json_decode($a->properties, true) : $a->properties;
                    $sertifikatAsesiMap[$a->subject_id] = $props['attributes']['asesi_id'] ?? null;
                }
            });

        $logs = Activity::with('causer.asesor', 'subject')
            ->where(function ($query) use ($sertifikasi, $allNewsIds, $allAsesmenIds, $allAsesisIds, $allSertifikatIds) {
                $hasClauses = false;

                if ($allNewsIds->isNotEmpty()) {
                    $query->where(function ($q) use ($allNewsIds) {
                        $q->where('subject_type', Pengumuman::class)
                            ->whereIn('subject_id', $allNewsIds);
                    });
                    $hasClauses = true;
                }
                if ($allAsesmenIds->isNotEmpty()) {
                    $query->orWhere(function ($q) use ($allAsesmenIds) {
                        $q->where('subject_type', Asesmen::class)
                            ->whereIn('subject_id', $allAsesmenIds);
                    });
                    $hasClauses = true;
                }
                if ($allAsesisIds->isNotEmpty()) {
                    $query->orWhere(function ($q) use ($allAsesisIds) {
                        $q->where('subject_type', Asesi::class)
                            ->whereIn('subject_id', $allAsesisIds);
                    });
                    $hasClauses = true;
                }
                if ($allSertifikatIds->isNotEmpty()) {
                    $query->orWhere(function ($q) use ($allSertifikatIds) {
                        $q->where('subject_type', Sertifikat::class)
                            ->whereIn('subject_id', $allSertifikatIds);
                    });
                    $hasClauses = true;
                }

                // Log Sertifikasi (subject = sertifikasi itu sendiri)
                $query->orWhere(function ($q) use ($sertifikasi) {
                    $q->where('subject_type', Sertifikasi::class)
                        ->where('subject_id', $sertifikasi->id);
                });
                $hasClauses = true;

                if (!$hasClauses) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('causer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->event, fn($q, $event) => $q->where('event', $event))
            ->when($request->subject_type, fn($q, $type) => $q->where('subject_type', $type))
            ->when($request->date_from, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(15)
            ->onEachSide(0)
            ->withQueryString();

        $logs->loadMissing('subject');
        $logs->loadMorph('subject', [
            Sertifikat::class => ['asesi.mahasiswa.user'],
        ]);

        return Inertia::render('Admin/LogSertifikasi', [
            'sertifikasi' => $sertifikasi,
            'logs' => $logs,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'subject_type', 'event']),
            'filterOptions' => [
                'subjects' => [Sertifikasi::class, Pengumuman::class, Asesmen::class, Asesi::class, Sertifikat::class],
            ],
            'sertifikatAsesiMap' => $sertifikatAsesiMap,
            'asesorMap' => Asesor::with('user')->get()->mapWithKeys(fn($a) => [$a->id => $a->user->name]),
        ]);
    }
}
