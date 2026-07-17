<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Traits\SendsPushNotifications;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use App\Models\Sertifikasi;
use App\Models\Asesi;
use App\Helpers\FileHelper;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class AsesmenController extends Controller
{
    use SendsPushNotifications;

    public function edit(Sertifikasi $sertifikasi, Request $request)
    {
        // dd($id);
        Gate::authorize('manageAssessment', $sertifikasi);
        NotificationController::markAsRead($request);
        $sertifikasi->load([
            'asesi.mahasiswa.user',
            'asesi',
            'skema',
            'asesor.user'
        ]);

        $asesorId = $request->user()->asesor?->id;
        $filteredAsesi = $sertifikasi->asesi
            ->where('status_berkas', 'sudah_lengkap')
            ->where('asesor_id', $asesorId)
            ->values();

        $asesmen = $sertifikasi->asesmen()->where('user_id', $request->user()->id)->with('user')->first();
        $sertifikasi->setRelation('asesmen', $asesmen);

        return Inertia::render('Admin/AsesmenAdmin', [
            'sertifikasi' => $sertifikasi,
            'filteredAsesi' => $filteredAsesi,
            'initialAsesiId' => $request->query('asesi_id'),
        ]);
    }

    public function update_tugas_asesmen(Sertifikasi $sertifikasi, Request $request)
    {
        // dd($request);
        Gate::authorize('manageAssessment', $sertifikasi);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'deadline' => 'nullable|date',
            'path_file' => 'nullable|file|mimes:zip,rar,txt,docx,pdf,pptx,xlsx|max:5120',
            'delete_files' => 'nullable|array',
            'send_notification' => 'boolean',
        ]);
        
        $sertifikasi->load('skema');
        $asesmen = $sertifikasi->asesmen()->firstOrNew([
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
            $asesis = Asesi::with(['mahasiswa.user'])
                ->where('sertifikasi_id', $sertifikasi->id)
                ->where('asesor_id', $asesorId)
                ->get();
            if ($asesis->isNotEmpty()) {
                $title = 'Update Tugas Asesmen';
                $body = 'Instruksi Tugas asesmen diperbaharui untuk sertifikasi ' . $sertifikasi->skema->nama_skema;
                foreach ($asesis as $asesi) {
                    $user = $asesi->mahasiswa->user ?? null;
                    $url = route('asesi.assessmen.index', [$sertifikasi, $asesi]);
                    $this->sendPushNotification($user, $title, $body, $url, 'TugasAsesmenBaru');
                }
            }
        }

        return redirect()->back()->with('message', 'Data berhasil disimpan!');
    }

    public function destroy(Sertifikasi $sertifikasi, Request $request)
    {
        Gate::authorize('manageAssessment', $sertifikasi);
        $asesmen = $sertifikasi->asesmen()->where('user_id', $request->user()->id)->first();
        if ($asesmen) {
            FileHelper::handleSingleFileDeletes($asesmen, ['path_file']);
            $asesmen->delete();
        }

        return redirect()->back()->with('message', 'Tugas Asesmen berhasil dihapus!');
    }
}
