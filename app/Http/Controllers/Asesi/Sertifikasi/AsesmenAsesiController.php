<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use App\Models\Sertification;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use App\Models\Asesiasesmenfile;
use App\Notifications\AsesiUploadTugasAsesmen;
use Inertia\Inertia;
use Illuminate\Support\Facades\Notification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification; // <-- IMPORT INI
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;

class AsesmenAsesiController extends Controller
{

    public function index_asesmen_asesi($sert_id, $asesi_id, Request $request)
    {
        // dd($id);
        NotificationController::markAsRead($request);
        $asesi = Asesi::with(['asesiasesmenfiles', 'transaction' => fn($q) => $q->latest()])->findOrFail($asesi_id);
        $asesi->latest_transaction = $asesi->transaction->first();
        return Inertia::render('Asesi/AsesmenAsesi', [
            'sertification' => Sertification::with('pembuatrinciantugasasesmen.asesor', 'asesmenfiles')->findOrFail($sert_id),
            'asesi' => $asesi
        ]);
    }

    public function update_asesmen_asesi($sert_id, $asesi_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $request->validate([
            'newFiles' => 'nullable|array|max:5',
            'newFiles.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB
            'delete_files' => 'nullable|array',
            'delete_files.*' => 'integer|exists:asesiasesmenfiles,id',
        ]);
        if ($request->filled('delete_files')) {
            $filesToDelete = Asesiasesmenfile::whereIn('id', $request->delete_files)->get();
            foreach ($filesToDelete as $file) {
                Storage::disk('public')->delete($file->path_file);
                $file->delete();
            }
        }
        if ($request->hasFile('newFiles')) {
            foreach ($request->file('newFiles') as $file) {
                $fileData = FileHelper::storeFileWithUniqueName($file, "asesi_files")['path'];
                Asesiasesmenfile::create([
                    'asesi_id' => $asesi_id,
                    'path_file' => $fileData,
                ]);
            }
        }
        $remainingFilesCount = Asesiasesmenfile::where('asesi_id', $asesi_id)->count();
        if ($remainingFilesCount === 0) {
            return redirect()->back()->withErrors(['newFiles' => 'Anda harus mengumpulkan setidaknya satu file.']);
        }
        $asesi = Asesi::with('student.user')->findOrFail($asesi_id);
        $sertification = Sertification::with(['asesor.user', 'skema'])
            ->findOrFail($sert_id);
        $asesor = $sertification->asesor->user;
        if ($asesor) {
            $body = $asesi->student->user->name . ' mengunggah tugas asesmen untuk sertifikasi ' . $sertification->skema->nama_skema;
            $url = route('admin.sertifikasi.rincian.assessment.asesi.index', ['sert_id' => $sertification->id, 'asesi_id' => $asesi_id]);
            NotificationLog::create([
                'user_id' => $asesor->id,
                'type' => 'AsesiUploadTugasAsesmen',
                'message' => $body,
                'link' => $url,
            ]);
            if ($asesor->fcm_token) {
                $message = CloudMessage::new()
                    ->withNotification(FirebaseNotification::create($body))
                    ->withData(['url' => $url]);
                try {
                    $messaging->send($message->toToken($asesor->fcm_token));
                } catch (NotFound $e) {
                    Log::warning("Token FCM tidak valid untuk user {$asesor->id}. Menghapus token.");
                    $asesor->update(['fcm_token' => null]);
                } catch (\Throwable $e) {
                    Log::error("Gagal mengirim notifikasi asesi mendaftar sertifikasi ke user {$asesor->id}: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('message', 'Berhasil unggah file asesmen.');
    }
}
