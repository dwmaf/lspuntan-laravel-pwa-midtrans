<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sertification;
use App\Models\Skema;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateVerificationController extends Controller
{
    public function index(Request $request)
    {
        $skemas = Skema::orderBy('nama_skema', 'asc')->get();

        $certificate = null;
        if ($request->filled('nomor_sertifikat') && $request->filled('skema_id')) {
            $foundCertificate = Sertifikat::where('nomor_sertifikat', $request->nomor_sertifikat)
                ->with(['asesi.student.user', 'asesi.sertification.skema'])
                ->first();

            if ($foundCertificate && $foundCertificate->asesi->sertification->skema_id == $request->skema_id) {
                $certificate = $foundCertificate;
            } else {
                return redirect()->back()->withErrors(['search' => 'Data sertifikat tidak ditemukan atau tidak cocok dengan skema yang dipilih.'])->withInput();
            }
        }

        return Inertia::render('Public/VerifyCertificate', [
            'skemas' => $skemas,
            'certificate' => $certificate,
            'input' => $request->only(['nomor_sertifikat', 'skema_id']),
        ]);
    }
}