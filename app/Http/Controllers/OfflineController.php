<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfflineController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $layout = 'layouts.guest';

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user->hasAnyRole(['admin','asesor'])) {
                $layout = 'layouts.admin';
            } elseif ($user->hasRole('asesi')) {
                $layout = 'layouts.app';
            }
        }

        // Kirim nama layout yang benar ke view
        return view('vendor.laravelpwa.offline', ['layout' => $layout]);
    }
}