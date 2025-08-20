<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Models\Transaction;
use App\Models\Sertifikat;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;

class PendaftarController extends Controller
{
    public function update_status_pembayaran($id, $transaction_id, Request $request)
    {
        // dd($request);
        $transaction = Transaction::find($transaction_id);
        // Memperbarui status sesuai dengan yang diterima dari form
        $transaction->status = $request->status;
        $transaction->save();

        return redirect()->back()->with('success', 'Status pembayaran asesi berhasil diperbarui!');
    }

    public function upload_certificate($id, $sert_id, Request $request)
    {
        $validatedData = $request->validate([
            'nomor_seri' => 'nullable|string|max:255',
            'nomor_sertifikat' => 'nullable|string|max:255',
            'nomor_registrasi' => 'nullable|string|max:255',
            'tanggal_terbit' => 'required|date',
            'berlaku_hingga' => 'required|date|after_or_equal:tanggal_terbit',
            'sertifikat_asesi' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
        ]);

        $asesi = Asesi::with('sertifikat')->find($id);
        // Cek apakah sudah ada sertifikat sebelumnya untuk menghapus file lama
        if ($asesi->sertifikat && $request->hasFile('sertifikat_asesi')) {
            if (Storage::disk('public')->exists($asesi->sertifikat->file_path)) {
                Storage::disk('public')->delete($asesi->sertifikat->file_path);
            }
        }

        // Simpan file baru jika ada
        if ($request->hasFile('sertifikat_asesi')) {
            $fileData = FileHelper::storeFileWithUniqueName($request->file('sertifikat_asesi'), 'sertifikat_asesi');
            $filePath = $fileData['path'];
        }

        // Gunakan updateOrCreate untuk membuat atau memperbarui sertifikat
        // Ini adalah cara paling efisien untuk relasi one-to-one
        $asesi->sertifikat()->updateOrCreate(
            ['asesi_id' => $asesi->id], // Kondisi untuk mencari
            [ // Data untuk di-update atau di-create
                'nomor_seri' => $validatedData['nomor_seri'],
                'nomor_sertifikat' => $validatedData['nomor_sertifikat'],
                'nomor_registrasi' => $validatedData['nomor_registrasi'],
                'tanggal_terbit' => $validatedData['tanggal_terbit'],
                'berlaku_hingga' => $validatedData['berlaku_hingga'],
                'file_path' => $filePath,
            ]
        );

        return back()->with('success', 'Sertifikat berhasil disimpan.');
    }

}
