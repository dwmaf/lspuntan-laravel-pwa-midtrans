<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sertifikasi;
use App\Models\Asesi;
use App\Models\User;
use App\Helpers\FileHelper;
use App\Models\Pengumuman;
use App\Enums\StatusFinalAsesi;
use App\Traits\SendsPushNotifications;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class PengumumanController extends Controller
{
    use SendsPushNotifications;

    private function notifyAsesi(Sertifikasi $sertifikasi, User $user, Pengumuman $pengumuman, string $title, string $type): void
    {
        $query = Asesi::with(['mahasiswa.user'])
            ->where('sertifikasi_id', $sertifikasi->id);

        if ($pengumuman->is_certif_news) {
            $query->where('status_final', StatusFinalAsesi::KOMPETEN);
        }

        if ($user->hasRole('admin')) {
            $asesis = $query->get();
        } else {
            $asesor = $user->asesor;
            $asesis = $asesor ? $query->where('asesor_id', $asesor->id)->get() : collect();
        }

        if ($asesis->isNotEmpty()) {
            $body = $title . ': ' . Str::limit($pengumuman->content, 100);
            foreach ($asesis as $asesi) {
                $recipient = $asesi->mahasiswa->user ?? null;
                $url = route('asesi.pengumuman.index', [$sertifikasi, $asesi, 'pengumuman_id' => $pengumuman->id]);
                $this->sendPushNotification($recipient, $title, $body, $url, $type);
            }
        }
    }

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
            'is_certif_news' => 'boolean',
        ]);
        $pengumuman = new Pengumuman([
            'user_id' => $request->user()->id,
            'sertifikasi_id' => $sertifikasi->id,
            'content' => $validatedData['content'],
            'is_certif_news' => $request->boolean('is_certif_news') && $request->user()->hasRole('admin'),
        ]);
        
        FileHelper::handleSingleFileUploads($pengumuman, ['path_file'], $request, 'sert_files');
        $pengumuman->save();

        if ($request->boolean('send_notification')) {
            $this->notifyAsesi($sertifikasi, $request->user(), $pengumuman, 'Pengumuman Baru', 'PengumumanBaru');
        }

        return redirect(route('admin.sertifikasi.pengumuman.index', $sertifikasi))->with('message', 'Berhasil membuat pengumuman');
    }


    public function update(Sertifikasi $sertifikasi, Pengumuman $pengumuman, Request $request)
    {
        Gate::authorize('update', $pengumuman);

        // dd($request);
        $validatedData = $request->validate([
            'content' => 'required|string',
            'path_file' => 'nullable|file|mimes:zip,rar,txt,docx,pdf,pptx,xlsx|max:5120',
            'delete_files' => 'nullable|array',
            'send_notification' => 'boolean',
            'is_certif_news' => 'boolean',
        ]);

        $pengumuman->content = $validatedData['content'];
        $pengumuman->is_certif_news = $request->boolean('is_certif_news') && $request->user()->hasRole('admin');
        FileHelper::handleSingleFileDeletes($pengumuman, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($pengumuman, ['path_file'], $request, 'sert_files');
        $pengumuman->save();

        if ($request->boolean('send_notification')) {
            $this->notifyAsesi($sertifikasi, $request->user(), $pengumuman, 'Pengumuman Diperbarui', 'PengumumanUpdated');
        }

        return redirect(route('admin.sertifikasi.pengumuman.index', $sertifikasi))->with('message', 'Pengumuman berhasil diupdate');
    }

    public function destroy(Sertifikasi $sertifikasi, Pengumuman $pengumuman)
    {
        Gate::authorize('delete', $pengumuman);

        FileHelper::handleSingleFileDeletes($pengumuman, ['path_file']);
        $pengumuman->delete();
        return redirect(route('admin.sertifikasi.pengumuman.index', $sertifikasi))->with('message', 'Berhasil menghapus pengumuman');
    }
}
