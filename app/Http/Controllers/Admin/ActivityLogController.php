<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Activity::with('causer')
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
                $query->whereDate('created_at','>=',$dateFrom);
            })
            ->when($request->input('date_to'), function ($query, $dateTo) {
                $query->whereDate('created_at','<=',$dateTo);
            })
            ->when($request->input('subject_type'), function ($query, $subject) {
                $query->where('subject_type',$subject);
            })
            ->when($request->input('event'), function ($query, $event) {
                $query->where('event',$event);
            })
            ->latest()
            ->paginate(15)
            ->onEachSide(0)
            ->withQueryString();
        $subjects = Activity::query()->select('subject_type')->whereNotNull('subject_type')->distinct()->pluck('subject_type');
        return Inertia::render('Admin/ActivityLog', [
            'logs' => $logs,
            'filters' => $request->only(['search','date_from','date_to','subject_type','event']),
            'filterOptions' => [
                'subjects' => $subjects,
            ],
        ]);
    }
}