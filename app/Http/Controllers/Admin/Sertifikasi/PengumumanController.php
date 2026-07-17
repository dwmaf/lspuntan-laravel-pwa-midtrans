<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertifikasi;
use App\Models\Asesi;
use App\Helpers\FileHelper;
use App\Models\Pengumuman;
use App\Traits\SendsPushNotifications;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class PengumumanController extends Controller
{
    use SendsPushNotifications;

    public function index(Sertifikasi $sertifikasi)
    {
        Gate::authorize('manageAnnouncement', $sertifikasi);
        $sertifikasi->load(['skema', 'asesor.user']);
        return Inertia::render('Admin/PengumumanAdmin', [
            'listPengumuman' => Inertia::scroll(
                Pengumuman::where('sertifikasi_id', $sertifikasi->id)
                    ->with('user.asesor')
                    ->latest()
                    ->paginate(10)
            ),
            'sertifikasi' => $sertifikasi,
        ]);
    }

    public function store(Sertifikasi $sertifikasi, Request $request)
    {
        Gate::authorize('manageAnnouncement', $sertifikasi);

        // dd($request);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'path_file' => 'nullable|file|mimes:zip,rar,txt,docx,pdf,pptx,xlsx|max:5120',
            'send_notification' => 'boolean',
        ]);
        $pengumuman = new Pengumuman([
            'user_id' => $request->user()->id,
            'sertifikasi_id' => $sertifikasi->id,
            'content' => $validatedData['content'],
        ]);
        
        FileHelper::handleSingleFileUploads($pengumuman, ['path_file'], $request, 'sert_files');
        $pengumuman->save();

        if ($request->boolean('send_notification')) {
            $asesis = Asesi::with(['mahasiswa.user'])
                ->where('sertifikasi_id', $sertifikasi->id)
                ->get();
            if ($asesis->isNotEmpty()) {
                $title = 'Pengumuman Baru';
                $body = 'Pengumuman baru: ' . Str::limit($pengumuman->content, 100);
                foreach ($asesis as $asesi) {
                    $user = $asesi->mahasiswa->user ?? null;
                    $url = route('asesi.pengumuman.index', [$sertifikasi, $asesi, 'pengumuman_id' => $pengumuman->id]);
                    $this->sendPushNotification($user, $title, $body, $url, 'PengumumanBaru');
                }
            }
        }

        return redirect(route('admin.sertifikasi.pengumuman.index', $sertifikasi))->with('message', 'Berhasil membuat pengumuman');
    }


    public function update(Sertifikasi $sertifikasi, Pengumuman $pengumuman, Request $request)
    {
        Gate::authorize('manageAnnouncement', $sertifikasi);

        // dd($request);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'path_file' => 'nullable|file|mimes:zip,rar,txt,docx,pdf,pptx,xlsx|max:5120',
            'delete_files' => 'nullable|array',
            'send_notification' => 'boolean',
        ]);

        $pengumuman->content = $validatedData['content'];
        FileHelper::handleSingleFileDeletes($pengumuman, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($pengumuman, ['path_file'], $request, 'sert_files');
        $pengumuman->save();

        if ($request->boolean('send_notification')) {
            $asesis = Asesi::with(['mahasiswa.user'])
                ->where('sertifikasi_id', $sertifikasi->id)
                ->get();

            if ($asesis->isNotEmpty()) {
                $title = 'Pengumuman Diperbarui';
                $body = 'Pengumuman diperbarui: ' . Str::limit($pengumuman->content, 100);
                foreach ($asesis as $asesi) {
                    $user = $asesi->mahasiswa->user ?? null;
                    $url = route('asesi.pengumuman.index', [$sertifikasi, $asesi, 'news_id' => $pengumuman->id]);
                    $this->sendPushNotification($user, $title, $body, $url, 'PengumumanUpdated');
                }
            }
        }

        return redirect(route('admin.sertifikasi.pengumuman.index', $sertifikasi))->with('message', 'Pengumuman berhasil diupdate');
    }

    public function destroy(Sertifikasi $sertifikasi, Pengumuman $pengumuman)
    {
        Gate::authorize('manageAnnouncement', $sertifikasi);

        FileHelper::handleSingleFileDeletes($pengumuman, ['path_file']);
        $pengumuman->delete();
        return redirect(route('admin.sertifikasi.pengumuman.index', $sertifikasi))->with('message', 'Berhasil menghapus pengumuman');
    }
}
