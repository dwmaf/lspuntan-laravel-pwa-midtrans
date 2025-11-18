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
use App\Traits\SendsPushNotifications;
use Illuminate\Support\Facades\Notification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;

class AsesmenAsesiController extends Controller
{
    use SendsPushNotifications;
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
            'newFiles.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'delete_files' => 'nullable|array',
            'delete_files.*' => 'integer|exists:asesiasesmenfiles,id',
        ]);
        FileHelper::handleCollectionFileDeletes(Asesiasesmenfile::class, $request->input('delete_files', []));
        FileHelper::handleCollectionFileUploads(Asesiasesmenfile::class, 'asesi_id', $asesi_id, $request, ['newFiles'], 'asesi_files');
        $remainingFilesCount = Asesiasesmenfile::where('asesi_id', $asesi_id)->count();
        if ($remainingFilesCount === 0) {
            return redirect()->back()->withErrors(['newFiles' => 'Anda harus mengumpulkan setidaknya satu file.']);
        }
        $asesi = Asesi::with('student.user')->findOrFail($asesi_id);
        $sertification = Sertification::with(['asesors.user', 'skema'])
            ->findOrFail($sert_id);
        if ($sertification->asesors->isNotEmpty()) {
            $title = 'Tugas Asesmen Dikumpulkan';
            $body = $asesi->student->user->name . ' mengunggah tugas asesmen untuk sertifikasi ' . $sertification->skema->nama_skema;
            $url = route('admin.sertifikasi.assessment.edit', [$sertification->id, 'asesi_id' => $asesi_id]);
            foreach ($sertification->asesors as $asesor) {
                $this->sendPushNotification(
                    $messaging, $asesor->user, $title, $body, $url, 'TugasAsesmenDikumpulkan'
                );
            }
        }

        return redirect()->back()->with('message', 'Berhasil unggah file asesmen.');
    }
}
