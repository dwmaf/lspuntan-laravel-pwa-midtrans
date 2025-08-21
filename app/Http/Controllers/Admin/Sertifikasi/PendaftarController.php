<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Models\Transaction;
use App\Models\Sertifikat;
use App\Notifications\StatusAsesiUpdated;
use App\Notifications\SertifikatDiunggah;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use App\Notifications\StatusBayarAsesiUpdated;

class PendaftarController extends Controller
{
    //buat nampilin daftar asesi yg udh daftar suatu sertifikasi di sisi admin
    public function list_asesi($sert_id, Request $request)
    {
        // dd($student);
        $sertification = Sertification::with('skema', 'asesi.transaction')->find($sert_id);
        return view('admin.sertifikasi.pendaftar.indexpendaftar', [
            'sertification' => $sertification,
        ]);
    }

    //buat nampilin rincian data asesi yg udh daftar suatu sertifikasi di sisi admin
    public function rincian_data_asesi($sert_id, $asesi_id, Request $request)
    {
        $asesi = Asesi::with('student.studentattachmentfile', 'transaction','sertifikat')->find($asesi_id);
        // dd($asesi->transaction);
        $sertification = $asesi->sertification;
        return view('admin.sertifikasi.pendaftar.rincianpendaftar', [
            'asesi' => $asesi,
            'sertification' => $sertification
        ]);
    }

    public function update_status_asesi($sert_id, $asesi_id, Request $request)
    {
        $asesi = Asesi::with('sertification')->find($asesi_id);

        // Memperbarui status sesuai dengan yang diterima dari form
        $asesi->status = $request->status;
        $asesi->save();
        
        $user = $asesi->student->user;
        $user->notify(new StatusAsesiUpdated($sert_id, $asesi->id, $asesi->status));
        return redirect()->back()->with('success', 'Status berhasil diperbarui');
    }

    public function update_status_pembayaran($sert_id, $transaction_id, Request $request)
    {
        // dd($request);
        $transaction = Transaction::find($transaction_id);
        // Memperbarui status sesuai dengan yang diterima dari form
        $transaction->status = $request->status;
        $transaction->save();
        $user = $transaction->asesi->student->user;
        $user->notify(new StatusBayarAsesiUpdated($sert_id, $transaction->asesi->id, $transaction->status));
        return redirect()->back()->with('success', 'Status pembayaran asesi berhasil diperbarui!');
    }

    public function upload_certificate($asesi_id, $sert_id, Request $request)
    {
        $validatedData = $request->validate([
            'nomor_seri' => 'nullable|string|max:255',
            'nomor_sertifikat' => 'nullable|string|max:255',
            'nomor_registrasi' => 'nullable|string|max:255',
            'tanggal_terbit' => 'required|date',
            'berlaku_hingga' => 'required|date|after_or_equal:tanggal_terbit',
            'sertifikat_asesi' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
        ]);

        $asesi = Asesi::with('sertifikat')->find($asesi_id);
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
        
        $user = $asesi->student->user;
        $user->notify(new SertifikatDiunggah($sert_id, $asesi->id));
        
        return back()->with('success', 'Sertifikat berhasil disimpan.');
    }

}
