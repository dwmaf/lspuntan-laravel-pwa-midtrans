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

    public function index_pengumuman_asesmen($sert_id, Request $request)
    {
        // dd($id);
        $sertification = Sertification::findOrFail($sert_id);

        // Hitung total asesi yang aktif (target pembaca)
        $totalAsesis = Asesi::where('sertification_id', $sert_id)
            ->where('status', 'dilanjutkan_asesmen')
            ->count();

        return Inertia::render('Admin/PengumumanAdmin', [
            'pengumumans' => Inertia::scroll(
                News::where('sertification_id', $sert_id)
                    ->with('user.asesor', 'newsfiles')
                    ->withCount('reads')
                    ->latest()
                    ->paginate(10)
            ),
            'sertification' => $sertification,
            'totalAsesis' => $totalAsesis,
        ]);
    }

    public function store_pengumuman_asesmen($sert_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'newFiles' => 'nullable|array|max:5',
            'newFiles.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,docx,doc,ppt,pptx,xls,xlsx',
            'is_published' => 'boolean',
            'send_notification' => 'boolean',
        ]);
        
        $newsParams = [
            'user_id' => $request->user()->id,
            'sertification_id' => $sert_id,
            'content' => $validatedData['content'],
        ];
        if ($request->boolean('is_published')) {
            $newsParams['published_at'] = now();
            $newsParams['content_created_at'] = now();
        }

        $news = News::create($newsParams);
        
        FileHelper::handleCollectionFileUploads(Newsfile::class, 'news_id', $news->id, $request, ['newFiles'], 'sert_files');
        
        $shouldSendNotification = $request->boolean('is_published') && $request->boolean('send_notification');

        if ($shouldSendNotification) {
            $asesis = Asesi::with(['student.user'])
                ->where('sertification_id', $sert_id)
                ->where('status', 'dilanjutkan_asesmen')
                ->get();
            if ($asesis->isNotEmpty()) {
                $title = 'Pengumuman Baru';
                $body = 'Pengumuman baru: ' . Str::limit($news->content, 100);
                foreach ($asesis as $asesi) {
                    $user = $asesi->student->user ?? null;
                    $url = route('asesi.pengumuman.index', [$sert_id, $asesi->id, 'news_id' => $news->id]);
                    $this->sendPushNotification($messaging, $user, $title, $body, $url, 'PengumumanBaru');
                }
            }
        }

        return redirect(route('admin.sertifikasi.assessment-announcement.index', $sert_id))->with('message', 'Berhasil membuat pengumuman');
    }


    public function update_pengumuman_asesmen($sert_id, $peng_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $request->validate([
            'content' => 'required|string',
            'newFiles' => 'nullable|array|max:5',
            'newFiles.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx,xls,xlsx',
            'delete_files' => 'nullable|array',
            'delete_files.*' => 'integer|exists:newsfiles,id',
            'is_published' => 'boolean',
            'send_notification' => 'boolean',

        ]);

        $news = News::findOrFail($peng_id);
        $news->content = $request->content;
        $hasMeaningfulChanges = $news->isDirty('content');
        if ($news->content_created_at && is_null($news->revised_at) && $request->boolean('is_published')) {
            if ($hasMeaningfulChanges) {
                $news->revised_at = now();
            }
        }
        if (is_null($news->published_at) && $request->boolean('is_published')) {
            $news->published_at = now();
            if (is_null($news->content_created_at)) {
                $news->content_created_at = now();
            }
        } else if (!$request->boolean('is_published')) {
            $news->published_at = null;
        }

        $news->save();
        FileHelper::handleCollectionFileDeletes(Newsfile::class, $request->input('delete_files', []));
        FileHelper::handleCollectionFileUploads(Newsfile::class, 'news_id', $news->id, $request, ['newFiles'], 'sert_files');

        $shouldSendNotification = $request->boolean('is_published') && $request->boolean('send_notification');

        if ($shouldSendNotification) {
            $asesis = Asesi::with(['student.user'])
                ->where('sertification_id', $sert_id)
                ->where('status', 'dilanjutkan_asesmen')
                ->get();
    
            if ($asesis->isNotEmpty()) {
                $title = 'Pengumuman Diperbarui';
                $body = 'Pengumuman diperbarui: ' . Str::limit($news->content, 100);
                foreach ($asesis as $asesi) {
                    $user = $asesi->student->user ?? null;
                    $url = route('asesi.pengumuman.index', [$sert_id, $asesi->id, 'news_id' => $news->id]);
                    $this->sendPushNotification($messaging, $user, $title, $body, $url, 'PengumumanUpdated');
                }
            }
        }

        return redirect(route('admin.sertifikasi.assessment-announcement.index', $sert_id))->with('message', 'Pengumuman berhasil diupdate');
    }

    public function destroy_pengumuman_asesmen($id, $peng_id, Request $request)
    {
        $pengumuman = News::with('newsfiles')->findOrFail($peng_id);
        $pengumuman->delete();
        return redirect(route('admin.sertifikasi.assessment-announcement.index', $id))->with('message', 'Berhasil menghapus pengumuman');
        // return response()->noContent();
    }

    public function getReaders($sert_id, $news_id, Request $request)
    {
        // 1. Ambil semua asesi yang relevan untuk sertifikasi ini
        $allAsesis = Asesi::with(['student.user'])
            ->where('sertification_id', $sert_id)
            ->where('status', 'dilanjutkan_asesmen')
            ->get();

        // 2. Ambil data pembaca untuk pengumuman ini
        $newsReaders = NewsRead::where('news_id', $news_id)
            ->pluck('read_at', 'user_id'); // [user_id => read_at]

        // 3. Gabungkan datanya
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

        // Urutkan: yang sudah baca di atas (opsional), atau urut nama
        // $readersStatus = $readersStatus->sortByDesc('has_read')->values(); 
        
        return response()->json($readersStatus);
    }
}
