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
        $asesi->load(['asesiasesmen']);
        return Inertia::render('Asesi/AsesmenAsesi', [
            'sertification' => $sertification->load('skema', 'asesmen.asesmenfiles'), 
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
            'ak_1' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('ak_1', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'ak_2' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('ak_2', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'ak_3' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('ak_3', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'ak_4' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('ak_4', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'ac_1' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('ac_1', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'map_1' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('map_1', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'ia_1' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('ia_1', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'ia_2' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('ia_2', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'ia_5' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('ia_5', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'ia_6' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('ia_6', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'ia_7' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('ia_7', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
        ]);
        $asesi_asesmen = Asesiasesmen::firstOrNew(['asesi_id'=>$asesi->id]);
        FileHelper::handleSingleFileDeletes($asesi_asesmen, $request->input('delete_files_asesi', []));
        FileHelper::handleSingleFileUploads($asesi_asesmen, ['ak_1', 'ak_2', 'ak_3', 'ak_4', 'ac_1', 'map_1', 'ia_2', 'ia_2', 'ia_5', 'ia_6', 'ia_7'], $request, 'asesi_files');
        $asesi_asesmen->save();
        $asesi->load('student.user');
        $sertification->load(['asesors.user', 'skema', 'asesmen']);

        // Validation: Check Deadline
        if ($sertification->asesmen->deadline && now()->greaterThan($sertification->asesmen->deadline)) {
             return redirect()->back()->withErrors(['ak_1' => 'Batas waktu pengumpulan tugas telah berakhir.']);
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
