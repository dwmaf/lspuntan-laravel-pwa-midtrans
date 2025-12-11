<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Enums\AsesiStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Traits\SendsPushNotifications;
use App\Models\Transaction;
use App\Models\Sertifikat;
use App\Notifications\StatusAsesiUpdated;
use App\Notifications\SertifikatDiunggah;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use App\Models\NotificationLog;
use App\Notifications\StatusBayarAsesiUpdated;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Illuminate\Validation\Rule;

class PendaftarController extends Controller
{
    use SendsPushNotifications;
    public function list_asesi($sert_id, Request $request)
    {
        // dd($student);
        $sertification = Sertification::with('skema', 'asesis.student.user', 'asesis.transaction')->findOrFail($sert_id);
        $sertification->asesis->each(function ($asesi) {
            $asesi->latest_transaction = $asesi->transaction->sortByDesc('created_at')->first();
        });
        return Inertia::render('Admin/PendaftarList', [
            'sertification' => $sertification,
        ]);
    }

    public function rincian_data_asesi($sert_id, $asesi_id, Request $request)
    {
        NotificationController::markAsRead($request);
        $asesi = Asesi::with(['student.user', 'asesifiles', 'makulnilais', 'transaction' => fn($q) => $q->latest(), 'sertifikat'])->findOrFail($asesi_id);
        $asesi->latest_transaction = $asesi->transaction->first();
        // dd($asesi->transaction);
        $sertification = Sertification::findOrFail($sert_id);
        return Inertia::render('Admin/PendaftarDetail', [
            'asesi' => $asesi,
            'sertification' => $sertification,
            'asesiStatusOptions' => collect(AsesiStatus::cases())->map(fn($case) => [
                'value' => $case->value,
                'label' => str_replace('_', ' ', $case->value),
            ]),
            'paymentStatusOptions' => collect(TransactionStatus::cases())->map(fn($case) => [
                'value' => $case->value,
                'label' => str_replace('_', ' ', $case->value),
            ]),
        ]);
    }

    public function update_status_asesi($sert_id, $asesi_id, Request $request, Messaging $messaging)
    {
        $asesi = Asesi::with('sertification')->find($asesi_id);

        $messageNotif = '';
        if ($request->status === 'dilanjutkan_asesmen') {
            $messageNotif = 'Selamat, Anda dilanjutkan ke asesmen, kini anda bisa mengakses form input bukti pembayaran dan pengumuman';
        } else if ($request->status === 'menunggu_verifikasi_berkas') {
            $messageNotif = 'Status Anda kembali menjadi daftar';
        } else if ($request->status === 'lulus_sertifikasi') {
            $messageNotif = 'Selamat, Anda dinyatakan lulus sertifikasi, silahkan tunggu Admin mengupload sertifikat Anda.';
        } else if ($request->status === 'perlu_perbaikan_berkas') {
            $messageNotif = 'Admin meminta anda untuk memperbaiki atau melengkapi berkas';
        } else if ($request->status === 'ditolak') {
            $messageNotif = 'Maaf, pengajuan sertifikasi Anda ditolak';
        }
        $asesi->status = $request->status;
        if ($request->status === 'perlu_perbaikan_berkas') {
            $asesi->catatan_perbaikan = $request->catatan_perbaikan;
        } else {
            $asesi->catatan_perbaikan = null;
        }
        $asesi->save();

        $user = $asesi->student->user;

        if ($user) {
            $title = 'Update Status Pengajuan';
            $body = $messageNotif;
            $url = route('asesi.sertifikasi.applied.show', ['sert_id' => $sert_id, 'asesi_id' => $asesi_id, 'messageNotif' => $messageNotif]);
            $this->sendPushNotification($messaging, $user, $title, $body, $url, 'StatusAsesiUpdated');
        }
        return redirect()->back()->with('message', 'Status asesi berhasil diperbarui');
    }

    public function update_status_pembayaran($sert_id, $transaction_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $messageNotif = '';
        if ($request->status === TransactionStatus::PENDING->value) {
            $messageNotif = 'Status pembayaran Anda sedang menunggu verifikasi.';
        } else if ($request->status === TransactionStatus::BUKTI_PEMBAYARAN_DITOLAK->value) {
            $messageNotif = 'Maaf, bukti pembayaran Anda ditolak.';
        } else if ($request->status === TransactionStatus::BUKTI_PEMBAYARAN_TERVERIFIKASI->value) {
            $messageNotif = 'Selamat, bukti pembayaran Anda telah diverifikasi.'; 
        } else if ($request->status === TransactionStatus::PERLU_PERBAIKAN_BUKTI_BAYAR->value) {
            $messageNotif = 'Admin meminta perbaikan bukti pembayaran.';
        }
        $transaction = Transaction::find($transaction_id);
        $transaction->status = $request->status;
        if ($request->status === TransactionStatus::PERLU_PERBAIKAN_BUKTI_BAYAR->value || $request->status === TransactionStatus::BUKTI_PEMBAYARAN_DITOLAK->value) {
            $transaction->catatan = $request->catatan;
        } else {
            $transaction->catatan = null;
        }
        $transaction->save();
        $user = $transaction->asesi->student->user;
        if ($user) {
            $title = 'Update Status Pembayaran';
            $body = $messageNotif;
            $url = route('asesi.sertifikasi.applied.show', ['sert_id' => $sert_id, 'asesi_id' => $transaction->asesi->id, 'messageNotif' => $messageNotif]);
            $this->sendPushNotification($messaging, $user, $title, $body, $url, 'StatusBayarAsesiUpdated');
        }
        return redirect()->back()->with('message', 'Status pembayaran asesi berhasil diperbarui!');
    }

    public function upload_certificate($asesi_id, $sert_id, Request $request, Messaging $messaging)
    {
        $asesi = Asesi::findOrFail($asesi_id);
        $sertifikat = $asesi->sertifikat()->firstOrNew(['asesi_id' => $asesi->id]);
        $validatedData = $request->validate([
            'nomor_seri' => 'nullable|string|max:255',
            'nomor_sertifikat' => 'nullable|string|max:255',
            'nomor_registrasi' => 'nullable|string|max:255',
            'tanggal_terbit' => 'required|date',
            'berlaku_hingga' => 'required|date|after_or_equal:tanggal_terbit',
            'file_path' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files', [])) && in_array('file_path', $request->input('delete_files', []));
                }),
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:2048'
            ],
            'delete_files' => 'nullable|array'
        ]);
        $sertifikat->fill($validatedData);
        FileHelper::handleSingleFileDeletes($sertifikat, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($sertifikat, ['bukti_bayar'], $request, 'bukti_bayar');
        FileHelper::saveIfDirty([$sertifikat]);

        $user = $asesi->student->user;
        if ($user) {
            $title = 'Sertifikat Telah Terbit';
            $body = 'Selamat! Sertifikat Anda telah diunggah.';
            $url = route('asesi.sertifikasi.applied.show', ['sert_id' => $sert_id, 'asesi_id' => $asesi_id]);
            $this->sendPushNotification($messaging, $user, $title, $body, $url, 'SertifikatUploaded');
        }

        return back()->with('message', 'Sertifikat berhasil disimpan.');
    }
}
