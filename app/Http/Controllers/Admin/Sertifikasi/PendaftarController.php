<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Enums\AsesiStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
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
        // dd($asesi->status, $asesi->id);
        if ($request->status === 'perlu_perbaikan_berkas') {
            $asesi->catatan_perbaikan = $request->catatan_perbaikan;
        } else {
            $asesi->catatan_perbaikan = null;
        }
        $asesi->save();

        $user = $asesi->student->user;

        if ($user) {
            $body = $messageNotif;
            $url = route('asesi.sertifikasi.applied.show', ['sert_id' => $sert_id, 'asesi_id' => $asesi_id, 'messageNotif' => $messageNotif]);
            NotificationLog::create([
                'user_id' => $user->id,
                'type' => 'StatusAsesiUpdated',
                'message' => $body,
                'link' => $url,
            ]);
            if ($user->fcm_token) {
                $message = CloudMessage::new()
                    ->withNotification(FirebaseNotification::create($body))
                    ->withData(['url' => $url]);
                try {
                    $messaging->send($message->toToken($user->fcm_token));
                } catch (NotFound $e) {
                    Log::warning("Token FCM tidak valid untuk user {$user->id}. Menghapus token.");
                    $user->update(['fcm_token' => null]);
                } catch (\Throwable $e) {
                    Log::error("Gagal mengirim notifikasi asesi mendaftar sertifikasi ke user {$user->id}: " . $e->getMessage());
                }
            }
        }
        return redirect()->back()->with('message', 'Status asesi berhasil diperbarui');
    }

    public function update_status_pembayaran($sert_id, $transaction_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $messageNotif = '';
        if ($request->status === 'dilanjutkan_asesmen') {
            $messageNotif = 'Selamat, Anda dilanjutkan ke asesmen, kini anda bisa mengakses form input bukti pembayaran dan pengumuman';
        } else if ($request->status === 'daftar') {
            $messageNotif = 'Status Anda kembali menjadi daftar';
        } else if ($request->status === 'lulus_sertifikasi') {
            $messageNotif = 'Selamat, Anda dinyatakan lulus sertifikasi, silahkan tunggu Admin mengupload sertifikat Anda.';
        } else if ($request->status === 'perlu_perbaikan_berkas') {
            $messageNotif = 'Admin meminta anda untuk memperbaiki atau melengkapi berkas';
        } else if ($request->status === 'ditolak') {
            $messageNotif = 'Maaf, pengajuan sertifikasi Anda ditolak';
        }
        $transaction = Transaction::find($transaction_id);
        // Memperbarui status sesuai dengan yang diterima dari form
        $transaction->status = $request->status;
        $transaction->save();
        $user = $transaction->asesi->student->user;
        $user->notify(new StatusBayarAsesiUpdated($sert_id, $transaction->asesi->id, $transaction->status));
        if ($user) {
            $body = $messageNotif;
            $url = route('admin.sertifikasi.rincian.assessment.asesi.index', ['sert_id' => $sert_id, 'asesi_id' => $transaction->asesi->id, 'messageNotif' => $messageNotif]);
            if ($user->fcm_token) {
                $message = CloudMessage::new()
                    ->withNotification(FirebaseNotification::create($body))
                    ->withData(['url' => $url]);
                try {
                    $messaging->send($message->toToken($user->fcm_token));
                } catch (NotFound $e) {
                    Log::warning("Token FCM tidak valid untuk user {$user->id}. Menghapus token.");
                    $user->update(['fcm_token' => null]);
                } catch (\Throwable $e) {
                    Log::error("Gagal mengirim notifikasi asesi mendaftar sertifikasi ke user {$user->id}: " . $e->getMessage());
                }
            }
        }
        return redirect()->back()->with('message', 'Status pembayaran asesi berhasil diperbarui!');
    }

    public function upload_certificate($asesi_id, $sert_id, Request $request, Messaging $messaging)
    {
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

        $asesi = Asesi::with('sertifikat')->find($asesi_id);
        $filePath = $asesi->sertifikat->file_path ?? null;
        // Cek apakah sudah ada sertifikat sebelumnya untuk menghapus file lama
        if ($request->has('delete_files')) {
            foreach ($request->delete_files as $fieldName) {
                if ($asesi->sertifikat->$fieldName) {
                    Storage::disk('public')->delete($asesi->sertifikat->$fieldName);
                    $asesi->sertifikat->$fieldName = null;
                }
            }
        }

        // Simpan file baru jika ada
        if ($request->hasFile('sertifikat_asesi')) {
            $filePath = FileHelper::storeFileWithUniqueName($request->file('sertifikat_asesi'), 'sertifikat_asesi')['path'];
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

        return back()->with('message', 'Sertifikat berhasil disimpan.');
    }
}
