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
use App\Traits\SendsPushNotifications;
use App\Notifications\PengumumanUpdated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;

class PengumumanController extends Controller
{
use SendsPushNotifications;
    
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
        FileHelper::handleCollectionFileUploads(Newsfile::class, 'news_id', $news->id, $request, ['newFiles'], 'sert_files');
        $asesis = Asesi::with(['student.user'])
            ->where('sertification_id', $sert_id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();
        if ($asesis->isNotEmpty()) {
            $title = 'Pengumuman Baru';
            $body = 'Pengumuman baru: ' . Str::limit($news->rincian, 100);
            foreach ($asesis as $asesi) {
                $user = $asesi->student->user ?? null;
                $url = route('asesi.pengumuman.index', [$sert_id, $asesi->id, 'news_id' => $news->news_id]);
                $this->sendPushNotification($messaging, $user, $title, $body, $url, 'PengumumanBaru');
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
        FileHelper::handleCollectionFileDeletes(Newsfile::class, $request->input('delete_files', []));
        FileHelper::handleCollectionFileUploads(Newsfile::class, 'news_id', $news->id, $request, ['newFiles'], 'sert_files');

        $asesis = Asesi::with(['student.user'])
            ->where('sertification_id', $sert_id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();
        
        if ($asesis->isNotEmpty()) {
            $title = 'Pengumuman Diperbarui';
            $body = 'Pengumuman diperbarui: ' . Str::limit($news->rincian, 100);
            foreach ($asesis as $asesi) {
                $user = $asesi->student->user ?? null;
                $url = route('asesi.pengumuman.index', [$sert_id, $asesi->id, 'news_id' => $news->id]);
                $this->sendPushNotification($messaging, $user, $title, $body, $url, 'PengumumanUpdated');
            }
        }

        return redirect(route('admin.sertifikasi.assessment-announcement.index', $sert_id))->with('message', 'Pengumuman berhasil diupdate!');
    }

    public function destroy_pengumuman_asesmen($id, $peng_id, Request $request)
    {
        $pengumuman = News::with('pengumumanasesmenfile')->findOrFail($peng_id);
        $pengumuman->delete();
        return redirect()->back()->with('message', 'Pengumuman berhasil dihapus');
    }
}
