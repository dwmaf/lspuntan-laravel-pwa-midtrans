<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OfflineController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        

        // Kirim nama layout yang benar ke view
        return view('vendor.laravelpwa.offline');
    }
}