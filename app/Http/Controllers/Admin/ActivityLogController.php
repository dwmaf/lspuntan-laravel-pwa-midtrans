<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = Activity::with('causer') 
            ->latest()
            ->paginate(20);
        return Inertia::render('Admin/ActivityLog', [
            'logs' => $logs,
        ]);
    }
}