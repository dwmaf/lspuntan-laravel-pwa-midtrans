<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skema;

class AnotherController extends Controller
{
    public function getAsesor($skemaId)
    {
        $asesors = Skema::find($skemaId)->asesors;
        return response()->json(['asesor' => $asesors]);
    }
}
