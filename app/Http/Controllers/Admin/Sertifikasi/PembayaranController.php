<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\User;
use App\Models\Asesi;
use App\Notifications\RincianPembayaranUpdated;
use Illuminate\Support\Facades\Notification;

class PembayaranController extends Controller
{

    // buat nampilin daftar sertifikasi yg tersedia di sisi asesi
    public function index_rincian_pembayaran($id, Request $request)
    {
        return view('admin.sertifikasi.pembayaran.indexpembayaran', [
            'sertification' => Sertification::with('pembuatrincianpembayaran.asesor')->find($id)
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
        $sertification->rincian_bayar_dibuat_oleh = $request->user()->id; // Ambil ID user yang login yg buat perubahan
        $sertification->rincian_bayar_dibuat_pada = now(); // Ambil waktu saat ini
        $sertification->save();
        
        // ambil semua Asesi yang cocok
        $asesis = Asesi::with(['student.user']) // pastikan relasi student->user ada
            ->where('sertification_id', $id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        // kirim notifikasi secara individual agar link bisa berisi asesi_id
        foreach ($asesis as $asesi) {
            $user = $asesi->student->user ?? null;
            if ($user) {
                $user->notify(new RincianPembayaranUpdated($sertification, $asesi));
            }
        }
        return redirect()->back()->with('success', 'Rincian pembayaran berhasil disimpan!');
    }

}
