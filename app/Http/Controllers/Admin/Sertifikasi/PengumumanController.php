<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Notifications\PengumumanBaru;
use App\Helpers\FileHelper;
use App\Models\News;
use App\Models\Newsfile;
use App\Models\NotificationLog;
use App\Notifications\PengumumanUpdated;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;

class PengumumanController extends Controller
{

    
    public function index_pengumuman_asesmen($sert_id, Request $request)
    {
        // dd($id);
        $sertification = Sertification::with('news.pembuatpengumuman.asesor', 'news.newsfiles')->findOrFail($sert_id);
        return Inertia::render('Admin/PengumumanAdmin', [
            'pengumumans' => $sertification->news,
            'sertification' => $sertification,
        ]);
    }
    
    public function store_pengumuman_asesmen($sert_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $validatedData = $request->validate([
            'rincian' => 'required|string',
            'newFiles' => 'nullable|array|max:5',
            'newFiles.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,docx,pptx,xls,xlsx',

        ]);
        $validatedData['madeby'] = $request->user()->id;
        $validatedData['sertification_id'] = $sert_id;
        $news = News::create(
            $validatedData
        );
        if ($request->hasFile('newFiles')) {
            foreach ($request->file('newFiles') as $file) {
                $path = FileHelper::storeFileWithUniqueName($file, 'sert_files')['path'];
                Newsfile::create([
                    'news_id' => $news->id,
                    'path_file' => $path,
                ]);
            }
        }
        $asesis = Asesi::with(['student.user'])
            ->where('sertification_id', $sert_id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        foreach ($asesis as $asesi) {
            $user = $asesi->student->user ?? null;
            $body = 'Pengumuman baru: ' . $news->rincian;
            $url = route('asesi.pengumuman.index', [$sert_id, $asesi->id, 'news_id' => $news->news_id]);
            if ($user) {
                NotificationLog::create([
                    'user_id' => $user->id,
                    'type' => 'PengumumanBaru',
                    'message' => $body,
                    'link' => $url,
                ]);
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

        return redirect()->back()->with('message', 'Berhasil membuat pengumuman');
    }

    
    public function update_pengumuman_asesmen($sert_id, $peng_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $request->validate([
            'rincian' => 'required|string',
            'newFiles' => 'nullable|array|max:5',
            'newFiles.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,docx,pptx,xls,xlsx',
            'delete_files' => 'nullable|array',
            'delete_files.*' => 'integer|exists:newsfiles,id',
        ]);

        $news = News::findOrFail($peng_id);
        $news->rincian = $request->rincian;
        $news->save();
        if ($request->filled('delete_files')) {
            $filesToDelete = Newsfile::whereIn('id', $request->delete_files)->get();
            foreach ($filesToDelete as $file) {
                Storage::disk('public')->delete($file->path_file);
                $file->delete();
            }
        }
        if ($request->hasFile('newFiles')) {
            foreach ($request->file('newFiles') as $file) {
                $path = FileHelper::storeFileWithUniqueName($file, 'sert_files')['path'];
                Newsfile::create([
                    'news_id' => $news->id,
                    'path_file' => $path,
                ]);
            }
        }

        $asesis = Asesi::with(['student.user'])
            ->where('sertification_id', $sert_id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();


        foreach ($asesis as $asesi) {
            $user = $asesi->student->user ?? null;
            $body = 'Pengumuman Updated: ' . $news->rincian;
            $url = route('asesi.pengumuman.index', [$sert_id, $asesi->id, 'news_id' => $news->news_id]);
            if ($user) {
                NotificationLog::create([
                    'user_id' => $user->id,
                    'type' => 'PengumumanUpdated',
                    'message' => $body,
                    'link' => $url,
                ]);
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

        return redirect(route('admin.sertifikasi.assessment-announcement.index', $sert_id))->with('message', 'Pengumuman berhasil diupdate!');
    }

    public function destroyPengumumanFile($sert_id, $id_file, Request $request)
    {
        $file = Newsfile::findOrFail($id_file);
        if ($file) {
            // Hapus file fisik
            if (Storage::disk('public')->exists($file->path_file)) {
                Storage::disk('public')->delete($file->path_file);
            }
            // Hapus record database
            $file->delete();
            return redirect()->back()->with('message', 'Lampiran berhasil dihapus.');
        }
    }
    public function destroy_pengumuman_asesmen($id, $peng_id, Request $request)
    {
        $pengumuman = News::with('pengumumanasesmenfile')->findOrFail($peng_id);
        $pengumuman->delete();
        return redirect()->back()->with('message', 'Pengumuman berhasil dihapus');
    }
}
