<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Models\Asesi;
use App\Models\Pengumumanasesmen;
use App\Models\Pengumumanasesmenfile;
use App\Models\Tugasasesmenattachmentfile;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Helpers\FileHelper;

class PembayaranController extends Controller
{

    // buat nampilin daftar sertifikasi yg tersedia di sisi asesi
    public function index_rincian_pembayaran($id, Request $request)
    {
        return view('admin.sertifikasi.pembayaran.indexpembayaran', [
            'sertification' => Sertification::find($id)
        ]);
    }

    public function update_rincian_pembayaran($id, Request $request)
    {
        // dd($request);
        $request->validate([
            'rincian_pembayaran' => 'required|string',
        ]);

        $sertification = Sertification::find($id);
        $sertification->rincian_pembayaran = $request->rincian_pembayaran;
        $sertification->rincian_pembayaran_dibuat_oleh = $request->user()->id; // Ambil ID user yang login yg buat perubahan
        $sertification->rincian_pembayaran_dibuat_pada = now(); // Ambil waktu saat ini
        $sertification->save();

        return redirect()->back()->with('success', 'Rincian pembayaran berhasil disimpan!');
    }

}
