<?php
// filepath: app/Http/Controllers/Public/CertificateVerificationController.php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Sertification;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateVerificationController extends Controller
{
    public function index(Request $request)
    {
        $sertifications = Sertification::with('skema')->where('status', '!=', 'belum_berlangsung')->orderBy('tgl_sertifikasi', 'desc')->get();
        $certificate = null;

        // Jika ada input pencarian
        if ($request->filled('nomor_sertifikat') && $request->filled('sertification_id')) {
            
            // Cari sertifikat berdasarkan nomor
            $foundCertificate = Sertifikat::where('nomor_sertifikat', $request->nomor_sertifikat)
                ->with(['asesi.student.user', 'asesi.sertification.skema']) // Eager load relasi
                ->first();

            // Validasi apakah sertifikat yang ditemukan sesuai dengan skema yang dipilih
            if ($foundCertificate && $foundCertificate->asesi->sertification_id == $request->sertification_id) {
                $certificate = $foundCertificate;
            } else {
                // Jika tidak cocok, kirim pesan error
                return redirect()->back()->withErrors(['search' => 'Data sertifikat tidak ditemukan atau tidak cocok.'])->withInput();
            }
        }

        return Inertia::render('Public/VerifyCertificate', [
            'sertifications' => $sertifications,
            'certificate' => $certificate,
            'input' => $request->only(['nomor_sertifikat', 'sertification_id']), // Untuk mengisi kembali form
        ]);
    }
}