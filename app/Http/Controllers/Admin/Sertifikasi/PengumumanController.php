<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Helpers\FileHelper;
use App\Models\News;
use App\Traits\SendsPushNotifications;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;


class PengumumanController extends Controller
{
    use SendsPushNotifications;

    public function index_pengumuman_asesmen(Sertification $sertification, Request $request)
    {
        $sertification->load('skema');
        $totalAsesis = Asesi::where('sertification_id', $sertification->id)
            ->count();

        return Inertia::render('Admin/PengumumanAdmin', [
            'pengumumans' => Inertia::scroll(
                News::where('sertification_id', $sertification->id)
                    ->with('user.asesor')
                    ->latest()
                    ->paginate(10)
            ),
            'sertification' => $sertification,
            'totalAsesis' => $totalAsesis,
        ]);
    }

    public function store_pengumuman_asesmen(Sertification $sertification, Request $request, Messaging $messaging)
    {
        // dd($request);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'path_file' => 'nullable|file|mimes:zip,rar,txt,docx,pdf,pptx,xlsx|max:5120',
            'send_notification' => 'boolean',
        ]);

        $news = News::create([
            'user_id' => $request->user()->id,
            'sertification_id' => $sertification->id,
            'content' => $validatedData['content'],
        ]);
        FileHelper::handleSingleFileUploads($news, ['path_file'], $request, 'sert_files');
        
        
        if ($request->boolean('send_notification')) {
            $asesis = Asesi::with(['student.user'])
                ->where('sertification_id', $sertification->id)
                ->get();
            if ($asesis->isNotEmpty()) {
                $title = 'Pengumuman Baru';
                $body = 'Pengumuman baru: ' . Str::limit($news->content, 100);
                foreach ($asesis as $asesi) {
                    $user = $asesi->student->user ?? null;
                    $url = route('asesi.pengumuman.index', [$sertification, $asesi, 'news_id' => $news->id]);
                    $this->sendPushNotification($messaging, $user, $title, $body, $url, 'PengumumanBaru');
                }
            }
        }

        return redirect(route('admin.sertifikasi.assessment-announcement.index', $sertification))->with('message', 'Berhasil membuat pengumuman');
    }


    public function update_pengumuman_asesmen(Sertification $sertification, News $news, Request $request, Messaging $messaging)
    {
        // dd($request);
        $request->validate([
            'content' => 'required|string',
            'path_file' => 'nullable|file|mimes:zip,rar,txt,docx,pdf,pptx,xlsx|max:5120',
            'delete_files' => 'nullable|array',
            'send_notification' => 'boolean',
        ]);

        $news->content = $request->content;
        FileHelper::handleSingleFileDeletes($news, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($news, ['path_file'], $request, 'sert_files');
        $news->save();

        if ($request->boolean('send_notification')) {
            $asesis = Asesi::with(['student.user'])
                ->where('sertification_id', $sertification->id)
                ->where('status', 'dilanjutkan_asesmen')
                ->get();
    
            if ($asesis->isNotEmpty()) {
                $title = 'Pengumuman Diperbarui';
                $body = 'Pengumuman diperbarui: ' . Str::limit($news->content, 100);
                foreach ($asesis as $asesi) {
                    $user = $asesi->student->user ?? null;
                    $url = route('asesi.pengumuman.index', [$sertification, $asesi, 'news_id' => $news->id]);
                    $this->sendPushNotification($messaging, $user, $title, $body, $url, 'PengumumanUpdated');
                }
            }
        }

        return redirect(route('admin.sertifikasi.assessment-announcement.index', $sertification))->with('message', 'Pengumuman berhasil diupdate');
    }

    public function destroy_pengumuman_asesmen(Sertification $sertification, News $news, Request $request)
    {
        FileHelper::handleSingleFileDeletes($news, ['path_file']);
        $news->delete();
        return redirect(route('admin.sertifikasi.assessment-announcement.index', $sertification))->with('message', 'Berhasil menghapus pengumuman');
    }
}
