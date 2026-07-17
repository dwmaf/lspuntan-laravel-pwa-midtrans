<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asesmen;
use App\Models\Skema;
use App\Models\Asesi;
use App\Models\Asesor;
use App\Models\Pengumuman;
use App\Models\Sertifikasi;
use App\Models\Sertifikat;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ], [
            'date_from.date' => 'Format tanggal awal tidak valid.',
            'date_to.date' => 'Format tanggal akhir tidak valid.',
            'date_to.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.',
        ]);

        $logs = Activity::with('causer.roles', 'subject')
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('causer', function ($causerQuery) use ($search) {
                        $causerQuery->where('name', 'like', "%{$search}%");
                    });
                    if (str_starts_with('sistem', strtolower($search))) {
                        $subQuery->orWhereNull('causer_id');
                    }
                });
            })
            ->when($request->input('date_from'), function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->input('date_to'), function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($request->input('subject_type'), function ($query, $subject) {
                $query->where('subject_type', $subject);
            })
            ->when($request->input('event'), function ($query, $event) {
                $query->where('event', $event);
            })
            ->whereNotIn('subject_type', [
                Sertifikasi::class,
                Asesi::class,
                Pengumuman::class,
                Asesmen::class,
                Sertifikat::class,
            ])
            ->latest()
            ->paginate(15)
            ->onEachSide(0)
            ->withQueryString();

        $logs->loadMissing('subject');
        $logs->getCollection()->loadMorph('subject', [
            Asesor::class => ['user'],
        ]);

        $subjects = Activity::query()->select('subject_type')->whereNotNull('subject_type')->whereNotIn('subject_type', [
            Sertifikasi::class,
            Asesi::class,
            Pengumuman::class,
            Asesmen::class,
            Sertifikat::class,
        ])->distinct()->pluck('subject_type');
        return Inertia::render('Admin/ActivityLog', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'subject_type', 'event']),
            'filterOptions' => [
                'subjects' => $subjects,
            ],
            'skemaMap' => Skema::all()->mapWithKeys(fn($s) => [$s->id => $s->nama_skema]),
        ]);
    }
}
