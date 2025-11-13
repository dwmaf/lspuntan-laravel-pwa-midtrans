<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Traits\SendsPushNotifications;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Models\NotificationLog;
use App\Models\Asesmenfile;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;

class AsesmenController extends Controller
{
    use SendsPushNotifications;
    public function edit($id, Request $request)
    {
        // dd($id);
        $sertification = Sertification::with([
            'asesis.transaction',
            'asesis.student.user',
            'asesis.asesiasesmenfiles',
            'asesmen.asesmenfiles'
        ])->findOrFail($id);

        $filteredAsesi = $sertification->asesis->filter(function ($asesi) {
            $latestTransaction = $asesi->transaction->sortByDesc('created_at')->first();
            return $asesi->status === 'dilanjutkan_asesmen'
                && $latestTransaction
                && $latestTransaction->status === 'bukti_pembayaran_terverifikasi';
        });

        return Inertia::render('Admin/AsesmenAdmin', [
            'sertification' => $sertification,
            'filteredAsesi' => $filteredAsesi,
            'initialAsesiId' => $request->query('asesi_id'),
        ]);
    }

    public function update_tugas_asesmen($sert_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'is_published' => 'required|boolean',
            'deadline' => 'nullable|date',
            'newFiles' => 'nullable|array|max:5',
            'newFiles.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,docx,pptx,xls,xlsx',
            'delete_files_collection' => 'nullable|array',
            'delete_files_collection.*' => 'integer|exists:asesmenfiles,id',
        ]);
        $sertification = Sertification::with('skema')->findOrFail($sert_id);
        $asesmen = $sertification->asesmen()->updateOrCreate(
            ['sertification_id' => $sertification->id],
            [
                'content' => $validatedData['content'],
                'deadline' => $validatedData['deadline'],
                'user_id' => $request->user()->id,
                'published_at' => $request->boolean('is_published') ? now() : null,
            ]
        );
        FileHelper::handleCollectionFileDeletes(Asesmenfile::class, $request->input('delete_files_collection', []));
        FileHelper::handleCollectionFileUploads(Asesmenfile::class, 'asesmen_id', $asesmen->id, $request, ['newFiles'], 'sert_files');

        $asesis = Asesi::with(['student.user'])
            ->where('sertification_id', $sert_id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        if ($asesis->isNotEmpty()) {
            $title = 'Update Tugas Asesmen';
            $body = 'Instruksi Tugas asesmen diperbaharui untuk sertifikasi ' . $sertification->skema->nama_skema;
            foreach ($asesis as $asesi) {
                $user = $asesi->student->user ?? null;
                $url = route('asesi.assessmen.index', ['sert_id' => $sertification->id, 'asesi_id' => $asesi->id]);
                $this->sendPushNotification(
                    $messaging,
                    $user,
                    $title,
                    $body,
                    $url,
                    'TugasAsesmenBaru'
                );
            }
        }

        return redirect()->back()->with('message', 'Data berhasil disimpan!');
    }
}
