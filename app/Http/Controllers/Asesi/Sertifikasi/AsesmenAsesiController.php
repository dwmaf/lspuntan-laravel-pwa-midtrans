<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Helpers\FileHelper;
use App\Models\Asesiasesmen;
use Inertia\Inertia;
use App\Traits\SendsPushNotifications;
use Kreait\Firebase\Contract\Messaging;
use Illuminate\Support\Facades\Gate;

class AsesmenAsesiController extends Controller
{
    use SendsPushNotifications;
    public function index(Sertification $sertification, Asesi $asesi, Request $request)
    {
        Gate::authorize('view', $asesi);
        NotificationController::markAsRead($request);
        return Inertia::render('Asesi/AsesmenAsesi', [
            'sertification' => $sertification->load('skema', 'asesmen'), 
            'asesi' => $asesi
        ]);
    }

    public function update(Sertification $sertification, Asesi $asesi, Request $request, Messaging $messaging)
    {
        Gate::authorize('update', $asesi);
        // dd($request);
        $request->validate([
            'delete_files_asesi' => 'nullable|array',
            'delete_files_asesi.*' => 'string',
            'path_file_asesmen' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('path_file_asesmen', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx,zip,rar',
                'max:5120'
            ],
        ]);
        FileHelper::handleSingleFileDeletes($asesi, $request->input('delete_files_asesi', []));
        FileHelper::handleSingleFileUploads($asesi, ['path_file_asesmen'], $request, 'asesi_files');
        $asesi->save();
        $asesi->load('student.user');
        $sertification->load(['asesors.user', 'skema', 'asesmen']);

        // Validation: Check Deadline
        if ($sertification->asesmen->deadline && now()->greaterThan($sertification->asesmen->deadline)) {
             return redirect()->back()->withErrors(['path_file_asesmen' => 'Batas waktu pengumpulan tugas telah berakhir.']);
        }
        if ($sertification->asesors->isNotEmpty()) {
            $title = 'Tugas Asesmen Dikumpulkan';
            $body = $asesi->student->user->name . ' mengunggah tugas asesmen untuk sertifikasi ' . $sertification->skema->nama_skema;
            $url = route('admin.sertifikasi.assessment.edit', [$sertification->id, 'asesi_id' => $asesi->id]);
            foreach ($sertification->asesors as $asesor) {
                $this->sendPushNotification(
                    $messaging, $asesor->user, $title, $body, $url, 'TugasAsesmenDikumpulkan'
                );
            }
        }

        return redirect()->back()->with('message', 'Berhasil unggah file asesmen.');
    }
}
