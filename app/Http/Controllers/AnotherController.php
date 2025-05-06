<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sertification;

class AnotherController extends Controller
{
    // public function getAsesor($skemaId)
    // {
    //     $asesors = Skema::find($skemaId)->asesors;
    //     return response()->json(['asesor' => $asesors]);
    // }
    // AsesiController.php
    public function apply_sertifikasi($id)
    {
        return view('asesi.sertifikasi.apply-page', [
            'sertification' => Sertification::with('skema')->find($id)
        ]);
    }
}
