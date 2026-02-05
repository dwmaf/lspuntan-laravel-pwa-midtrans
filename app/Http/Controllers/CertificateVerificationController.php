<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sertification;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateVerificationController extends Controller
{
    public function index(Request $request)
    {
        $sertifications = Sertification::with('skema')->where('status', '!=', 'belum_berlangsung')->get();
        $certificate = null;
        if ($request->filled('nomor_sertifikat') && $request->filled('sertification_id')) {
            $foundCertificate = Sertifikat::where('nomor_sertifikat', $request->nomor_sertifikat)
                ->with(['asesi.student.user', 'asesi.sertification.skema'])
                ->first();

            if ($foundCertificate && $foundCertificate->asesi->sertification_id == $request->sertification_id) {
                $certificate = $foundCertificate;
            } else {
                return redirect()->back()->withErrors(['search' => 'Data sertifikat tidak ditemukan atau tidak cocok.'])->withInput();
            }
        }

        return Inertia::render('Public/VerifyCertificate', [
            'sertifications' => $sertifications,
            'certificate' => $certificate,
            'input' => $request->only(['nomor_sertifikat', 'sertification_id']),
        ]);
    }
}