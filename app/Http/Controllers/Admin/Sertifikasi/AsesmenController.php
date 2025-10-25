<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
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
    public function edit($id)
    {
        // dd($id);
        $sertification = Sertification::with([
            'asesi.transaction',
            'asesi.student.user',
            'asesi.asesiasesmenfiles',
            'pembuatrinciantugasasesmen',
            'asesmenfiles'
        ])->findOrFail($id);

        $filteredAsesi = $sertification->asesi->filter(function ($asesi) {
            $latestTransaction = $asesi->transaction->sortByDesc('created_at')->first();
            return $asesi->status === 'dilanjutkan_asesmen'
                && $latestTransaction
                && $latestTransaction->status === 'bukti_pembayaran_terverifikasi';
        });

        return Inertia::render('Admin/AsesmenAdmin', [
            'sertification' => $sertification,
            'filteredAsesi' => $filteredAsesi
        ]);
    }

    public function update_tugas_asesmen($sert_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $request->validate([
            'rincian_tugas_asesmen' => 'required|string',
            'batas_pengumpulan_tugas_asesmen' => 'nullable|date',
            'newFiles' => 'nullable|array|max:5',
            'newFiles.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,docx,pptx,xls,xlsx',
            'delete_files_collection' => 'nullable|array',
            'delete_files_collection.*' => 'integer|exists:asesmenfiles,id',
        ]);
        if ($request->has('delete_files_collection')) {
            $filesToDelete = Asesmenfile::whereIn('id', $request->delete_files_collection)->get();
            foreach ($filesToDelete as $file) {
                Storage::disk('public')->delete($file->path_file);
                $file->delete();
            }
        }
        $sertification = Sertification::findOrFail($sert_id);
        $sertification->rincian_tugas_asesmen = $request->rincian_tugas_asesmen;
        $sertification->batas_pengumpulan_tugas_asesmen = $request->batas_pengumpulan_tugas_asesmen;
        $sertification->tugasasesmen_madeby = $request->user()->id;
        if (is_null($sertification->tugasasesmen_createdat)) {
            $sertification->tugasasesmen_createdat = now();
        } else {
            $sertification->tugasasesmen_updatedat = now();
        }
        $sertification->save();

        if ($request->hasFile('newFiles')) {
            foreach ($request->file('newFiles') as $file) {
                if ($file->isValid()) {
                    $path = FileHelper::storeFileWithUniqueName($file, 'sert_files')['path'];
                    Asesmenfile::create([
                        'sertification_id' => $sert_id,
                        'path_file' => $path,
                    ]);
                }
            }
        }

        $asesis = Asesi::with(['student.user'])
            ->where('sertification_id', $sert_id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        if ($asesis->isNotEmpty()) {
            foreach ($asesis as $asesi) {
                $user = $asesi->student->user ?? null;
                $body = 'Instruksi Tugas asesmen diperbaharui untuk sertifikasi ' . $sertification->skema->nama_skema;
                $url = route('asesi.assessmen.index', ['sert_id' => $sertification->id, 'asesi_id' => $asesi->id]);
                if ($user) {
                    NotificationLog::create([
                        'user_id' => $user->id,
                        'type' => 'TugasAsesmenBaru',
                        'message' => $body,
                        'link' => $url,
                    ]);
                }
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
        }

        return redirect()->back()->with('message', 'Data berhasil disimpan!');
    }
}
