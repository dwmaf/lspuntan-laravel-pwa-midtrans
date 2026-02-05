<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Traits\SendsPushNotifications;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Models\Asesmenfile;
use App\Helpers\FileHelper;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;

class AsesmenController extends Controller
{
    use SendsPushNotifications;
    public function edit(Sertification $sertification, Request $request)
    {
        // dd($id);
        $sertification->load([
            'asesis.student.user',
            'asesis',
            'asesmen',
            'skema'
        ]);

        $filteredAsesi = $sertification->asesis->where('status_akses_asesmen','diberikan')->values();
        // dd($filteredAsesi);

        return Inertia::render('Admin/AsesmenAdmin', [
            'sertification' => $sertification,
            'filteredAsesi' => $filteredAsesi,
            'initialAsesiId' => $request->query('asesi_id'),
        ]);
    }

    public function update_tugas_asesmen(Sertification $sertification, Request $request, Messaging $messaging)
    {
        // dd($request);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'deadline' => 'nullable|date',
            'path_file' => 'nullable|file|mimes:zip,rar,txt,docx,pdf,pptx,xlsx|max:5120',
            'delete_files' => 'nullable|array',
            'send_notification' => 'boolean',
        ]);
        
        $sertification->load('skema');
        $asesmen = $sertification->asesmen()->firstOrNew([]);
        $asesmen->fill([
            'content' => $validatedData['content'],
            'deadline' => $validatedData['deadline'],
            'user_id' => $request->user()->id,
        ]);
        FileHelper::handleSingleFileDeletes($asesmen, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($asesmen, ['path_file'], $request, 'sert_files');
        $asesmen->save();
        if ($request->boolean('send_notification')) {
            $asesis = Asesi::with(['student.user'])
                ->where('sertification_id', $sertification->id)
                ->where('status_akses_asesmen', 'diberikan')
                ->get();
            if ($asesis->isNotEmpty()) {
                $title = 'Update Tugas Asesmen';
                $body = 'Instruksi Tugas asesmen diperbaharui untuk sertifikasi ' . $sertification->skema->nama_skema;
                foreach ($asesis as $asesi) {
                    $user = $asesi->student->user ?? null;
                    $url = route('asesi.assessmen.index', [$sertification, $asesi]);
                    $this->sendPushNotification($messaging, $user, $title, $body, $url, 'TugasAsesmenBaru');
                }
            }
        }

        return redirect()->back()->with('message', 'Data berhasil disimpan!');
    }

    public function destroy(Sertification $sertification)
    {
        if ($sertification->asesmen) {
            FileHelper::handleSingleFileDeletes($sertification->asesmen, ['path_file']);
            $sertification->asesmen->delete();
        }

        return redirect()->back()->with('message', 'Tugas Asesmen berhasil dihapus!');
    }
}
