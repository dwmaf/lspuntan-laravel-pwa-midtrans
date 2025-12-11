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
            'send_notification' => 'boolean',
        ]);
        $sertification = Sertification::with('skema')->findOrFail($sert_id);
        $asesmen = $sertification->asesmen()->firstOrNew([]);
        $asesmen->fill([
            'content' => $validatedData['content'],
            'deadline' => $validatedData['deadline'],
            'user_id' => $request->user()->id,
        ]);
        $isFirstTimePublish = is_null($asesmen->published_at) && $request->boolean('is_published') && is_null($asesmen->content_created_at);
        $hasMeaningfulChanges = $asesmen->isDirty('content') || $sertification->isDirty('deadline');
        $shouldSendNotification = $request->boolean('is_published') && $request->boolean('send_notification');
        
        if ($isFirstTimePublish) {
            $asesmen->content_created_at = now();
        }
        if ($asesmen->content_created_at && is_null($asesmen->revised_at) && $request->boolean('is_published')) {
            if ($hasMeaningfulChanges) {
                $asesmen->revised_at = now();
            }
        }
        if (is_null($asesmen->published_at) && $request->boolean('is_published')) {
            $asesmen->published_at = now();
        } else if (!$request->boolean('is_published')) {
            $asesmen->published_at = null;
        }
        $asesmen->save();
        FileHelper::handleCollectionFileDeletes(Asesmenfile::class, $request->input('delete_files_collection', []));
        FileHelper::handleCollectionFileUploads(Asesmenfile::class, 'asesmen_id', $asesmen->id, $request, ['newFiles'], 'sert_files');
        if ($shouldSendNotification) {
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
                    $this->sendPushNotification($messaging, $user, $title, $body, $url, 'TugasAsesmenBaru');
                }
            }
        }

        return redirect()->back()->with('message', 'Data berhasil disimpan!');
    }

    public function destroy($sert_id)
    {
        $sertification = Sertification::findOrFail($sert_id);
        if ($sertification->asesmen) {
            $sertification->asesmen->delete();
        }

        return redirect()->back()->with('message', 'Tugas Asesmen berhasil dihapus!');
    }
}
