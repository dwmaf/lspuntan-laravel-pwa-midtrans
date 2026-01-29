<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Models\NewsRead;

use App\Helpers\FileHelper;
use App\Models\News;
use App\Models\Newsfile;

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
                    ->with('user.asesor', 'newsfiles')
                    ->withCount('reads')
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
            'newFiles' => 'nullable|array|max:5',
            'newFiles.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,docx,doc,ppt,pptx,xls,xlsx',
            'send_notification' => 'boolean',
        ]);
        
        $newsParams = [
            'user_id' => $request->user()->id,
            'sertification_id' => $sertification->id,
            'content' => $validatedData['content'],
        ];

        $news = News::create($newsParams);
        
        FileHelper::handleCollectionFileUploads(Newsfile::class, 'news_id', $news->id, $request, ['newFiles'], 'sert_files');
        
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
            'newFiles' => 'nullable|array|max:5',
            'newFiles.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx,xls,xlsx',
            'delete_files' => 'nullable|array',
            'delete_files.*' => 'integer|exists:newsfiles,id',
            'send_notification' => 'boolean',

        ]);

        $news->content = $request->content;

        $news->save();
        FileHelper::handleCollectionFileDeletes(Newsfile::class, $request->input('delete_files', []));
        FileHelper::handleCollectionFileUploads(Newsfile::class, 'news_id', $news->id, $request, ['newFiles'], 'sert_files');

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
        $news->delete();
        return redirect(route('admin.sertifikasi.assessment-announcement.index', $sertification))->with('message', 'Berhasil menghapus pengumuman');
    }

    public function getReaders(Sertification $sertification, News $news, Request $request)
    {
        $allAsesis = Asesi::with(['student.user'])
            ->where('sertification_id', $sertification->id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        $newsReaders = NewsRead::where('news_id', $news->id)
            ->pluck('read_at', 'user_id');

        $readersStatus = $allAsesis->map(function ($asesi) use ($newsReaders) {
            $user = $asesi->student->user;
            $userId = $user->id ?? null;
            $hasRead = $userId && $newsReaders->has($userId);
            
            return [
                'name' => $user->name ?? 'Unknown',
                'email' => $user->email ?? '-',
                'nim' => $asesi->student->nim ?? '-',
                'has_read' => $hasRead,
                'read_at' => $hasRead ? $newsReaders[$userId] : null,
            ];
        });
        
        return response()->json($readersStatus);
    }
}
