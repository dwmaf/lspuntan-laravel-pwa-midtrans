<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Traits\SendsPushNotifications;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Models\Asesmenfile;
use App\Helpers\FileHelper;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class AsesmenController extends Controller
{
    use SendsPushNotifications;

    public function edit(Sertification $sertification, Request $request)
    {
        // dd($id);
        Gate::authorize('manageAssessment', $sertification);
        NotificationController::markAsRead($request);
        $sertification->load([
            'asesis.student.user',
            'asesis',
            'skema',
            'asesors.user'
        ]);

        $asesorId = $request->user()->asesor?->id;
        $filteredAsesi = $sertification->asesis
            ->where('status_berkas', 'sudah_lengkap')
            ->where('asesor_id', $asesorId)
            ->values();

        $asesmen = $sertification->asesmen()->where('user_id', $request->user()->id)->with('user')->first();
        $sertification->setRelation('asesmen', $asesmen);

        return Inertia::render('Admin/AsesmenAdmin', [
            'sertification' => $sertification,
            'filteredAsesi' => $filteredAsesi,
            'initialAsesiId' => $request->query('asesi_id'),
        ]);
    }

    public function update_tugas_asesmen(Sertification $sertification, Request $request)
    {
        // dd($request);
        Gate::authorize('manageAssessment', $sertification);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'deadline' => 'nullable|date',
            'path_file' => 'nullable|file|mimes:zip,rar,txt,docx,pdf,pptx,xlsx|max:5120',
            'delete_files' => 'nullable|array',
            'send_notification' => 'boolean',
        ]);
        
        $sertification->load('skema');
        $asesmen = $sertification->asesmen()->firstOrNew([
            'user_id' => $request->user()->id,
        ]);
        $asesmen->fill([
            'content' => $validatedData['content'],
            'deadline' => $validatedData['deadline'],
            'user_id' => $request->user()->id,
        ]);
        FileHelper::handleSingleFileDeletes($asesmen, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($asesmen, ['path_file'], $request, 'sert_files');
        $asesmen->save();
        // Kirim push notif ke semua asesi yg diampu oleh asesor yang membuat tugas asesmen
        if ($request->boolean('send_notification')) {
            $asesorId = $request->user()->asesor?->id;
            $asesis = Asesi::with(['student.user'])
                ->where('sertification_id', $sertification->id)
                ->where('asesor_id', $asesorId)
                ->get();
            if ($asesis->isNotEmpty()) {
                $title = 'Update Tugas Asesmen';
                $body = 'Instruksi Tugas asesmen diperbaharui untuk sertifikasi ' . $sertification->skema->nama_skema;
                foreach ($asesis as $asesi) {
                    $user = $asesi->student->user ?? null;
                    $url = route('asesi.assessmen.index', [$sertification, $asesi]);
                    $this->sendPushNotification($user, $title, $body, $url, 'TugasAsesmenBaru');
                }
            }
        }

        return redirect()->back()->with('message', 'Data berhasil disimpan!');
    }

    public function destroy(Sertification $sertification, Request $request)
    {
        Gate::authorize('manageAssessment', $sertification);
        $asesmen = $sertification->asesmen()->where('user_id', $request->user()->id)->first();
        if ($asesmen) {
            FileHelper::handleSingleFileDeletes($asesmen, ['path_file']);
            $asesmen->delete();
        }

        return redirect()->back()->with('message', 'Tugas Asesmen berhasil dihapus!');
    }
}
