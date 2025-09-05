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
            'sertification' => Sertification::with('pembuatrincianpembayaran')->find($id)
        ]);
    }

    public function update_rincian_pembayaran($id, Request $request)
    {
        // dd($request);
        $request->validate([
            'rincian_pembayaran' => 'required|string',
            'tgl_bayar_ditutup' => 'required|date',
            'harga' => 'required|numeric|min:0',
        ]);

        $sertification = Sertification::findOrFail($id);
        $sertification->fill($request->only([
            'rincian_pembayaran',
            'tgl_bayar_ditutup',
            'harga',
        ]));
        $sertification->rincian_pembayaran = $request->rincian_pembayaran;
        $sertification->rincianbayar_madeby = $request->user()->id; // Ambil ID user yang login yg buat perubahan
        if(is_null($sertification->rincianbayar_createdat)) {
            $sertification->rincianbayar_createdat = now(); // Ambil waktu saat ini
        } else{
            $sertification->rincianbayar_updatedat = now(); // Ambil waktu saat ini
        }
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
