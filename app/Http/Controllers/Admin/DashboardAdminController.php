<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Helpers\FileHelper;
use App\Models\Asesi;
use App\Models\Sertification;
use Inertia\Inertia;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $pendingRegistrants = Asesi::with(['student.user', 'sertification.skema'])
                                ->latest()
                                ->take(5)
                                ->get();
        $sertificationBerlangsung = Sertification::with('skema')->withCount('asesis')->where('status', 'berlangsung')->get();
        return Inertia::render('Admin/DashboardAdmin', [
            'pendingRegistrants' => $pendingRegistrants,
            'sertificationBerlangsung' => $sertificationBerlangsung,
        ]);
    }

}
