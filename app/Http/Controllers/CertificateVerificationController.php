<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Skema;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateVerificationController extends Controller
{
    public function index(Request $request)
    {
        $listSkema = Skema::orderBy('nama_skema', 'asc')->get();

        $certificate = null;
        $errors = [];
        if ($request->filled('nomor_sertifikat') && $request->filled('skema_id')) {
            $foundCertificate = Sertifikat::where('nomor_sertifikat', $request->nomor_sertifikat)
                ->with(['asesi.mahasiswa.user', 'asesi.sertifikasi.skema'])
                ->first();

            if ($foundCertificate && $foundCertificate->asesi->sertifikasi->skema_id == $request->skema_id) {
                $certificate = $foundCertificate;
            } else {
                $errors = ['search' => 'Data sertifikat tidak ditemukan atau tidak cocok dengan skema yang dipilih.'];
            }
        }

        return Inertia::render('Public/VerifyCertificate', [
            'listSkema' => $listSkema,
            'certificate' => $certificate,
            'input' => $request->only(['nomor_sertifikat', 'skema_id']),
            'errors' => $errors,
        ]);
    }
}