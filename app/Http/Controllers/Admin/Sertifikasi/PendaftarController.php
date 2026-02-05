<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Enums\StatusAksesMenuAsesmen;
use App\Enums\StatusBerkasAdministrasi;
use App\Enums\StatusFinalAsesi;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Traits\SendsPushNotifications;
use App\Helpers\FileHelper;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class PendaftarController extends Controller
{
    use SendsPushNotifications;
    public function listAsesi(Sertification $sertification, Request $request)
    {
        // dd($student);
        $sertification->load('skema', 'asesis.student.user');
        
        return Inertia::render('Admin/PendaftarList', [
            'sertification' => $sertification,
        ]);
    }

    public function rincianDataAsesi(Sertification $sertification, Asesi $asesi, Request $request)
    {
        Gate::authorize('view', $asesi);
        NotificationController::markAsRead($request);
        $sertification->load('skema');
        $asesi->load(['student.user', 'asesifiles', 'sertifikat']);
        
        return Inertia::render('Admin/PendaftarDetail', [
            'asesi' => $asesi,
            'sertification' => $sertification,
            'statusAksesMenuAsesmenOptions' => StatusAksesMenuAsesmen::options(),
            'statusBerkasAdministrasiOptions' => StatusBerkasAdministrasi::options(),
            'StatusFinalAsesiOptions' => StatusFinalAsesi::options(),
        ]);
    }

    public function updateStatusBerkas(Sertification $sertification, Asesi $asesi, Request $request, Messaging $messaging)
    {
        Gate::authorize('update', $asesi);
        $messageNotif = '';
        if ($request->status_berkas === StatusBerkasAdministrasi::SUDAH_LENGKAP->value) {
            $messageNotif = 'Berkas Anda telah dinyatakan lengkap.';
        } else if ($request->status_berkas === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS->value) {
            $messageNotif = 'Ada berkas yang perlu anda perbaiki.';
        }
        
        $asesi->update([
            'status_berkas' => $request->status_berkas,
            'catatan_perbaikan' => ($request->status_berkas === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS->value) ? $request->catatan_perbaikan : null,
        ]);

        $user = $asesi->student->user;
        if ($user) $this->sendPushNotification($messaging, $user, 'Update Status Pengajuan Asesi', $messageNotif, route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]), 'StatusAsesiUpdated');
        return redirect()->back()->with('message', 'Status asesi berhasil diperbarui');
    }

    public function updateAksesAsesmen(Sertification $sertification, Asesi $asesi, Request $request, Messaging $messaging)
    {
        Gate::authorize('update', $asesi);
        // dd($request);
        if ($request->status_akses_asesmen === StatusAksesMenuAsesmen::BELUM_DIBERIKAN->value) {
            $messageNotif = 'Hak akses ke menu asesmen anda belum diberikan.';
        } else if ($request->status_akses_asesmen === StatusAksesMenuAsesmen::DIBERIKAN->value) {
            $messageNotif = 'Hak akses ke menu asesmen telah diberikan.';
        }
        
        $asesi->update(['status_akses_asesmen'=>$request->status_akses_asesmen]);
        $user = $asesi->student->user;
        if ($user) {
            $title = 'Update Akses Menu Asesmen';
            $body = $messageNotif;
            $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]);
            $this->sendPushNotification($messaging, $user, $title, $body, $url, 'StatusAsesiUpdated');
        }
        return redirect()->back()->with('message', 'Hak akses asesi ke menu asesmen berhasil diperbarui!');
    }

    public function updateStatusFinal(Sertification $sertification, Asesi $asesi, Request $request, Messaging $messaging)
    {
        $messageNotif = '';
        if ($request->status_final === StatusFinalAsesi::KOMPETEN->value) {
            $messageNotif = 'Selamat, Anda dinyatakan Kompeten pada skema sertifikasi ini.';
        } else if ($request->status_final === StatusFinalAsesi::BELUM_KOMPETEN->value) {
            $messageNotif = 'Maaf, Anda dinyatakan Belum Kompeten pada skema sertifikasi ini.';
        } else if ($request->status_final === StatusFinalAsesi::DISKUALIFIKASI->value) {
            $messageNotif = 'Maaf, Anda dinyatakan Diskualifikasi.';
        } else if ($request->status_final === StatusFinalAsesi::BELUM_DITETAPKAN->value) {
            $messageNotif = 'Status Akhir Anda telah direset menjadi Belum Ditetapkan.';
        }

        $asesi->update(['status_final' => $request->status_final]);

        $user = $asesi->student->user;
        if ($user) {
            $title = 'Update Status Final';
            $body = $messageNotif;
            $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]);
            $this->sendPushNotification($messaging, $user, $title, $body, $url, 'StatusAsesiUpdated');
        }
        return redirect()->back()->with('message', 'Status akhir asesi berhasil diperbarui');   
    }
    public function updateAksesAsesmenBulk(Sertification $sertification, Request $request, Messaging $messaging)
    {
        $request->validate([
            'asesi_ids' => 'required|array',
            'asesi_ids.*' => 'exists:asesis,id',
            'status_akses_asesmen' => ['required', Rule::in(['belum_diberikan', 'diberikan'])],
        ]);

        $messageNotif = '';
        if ($request->status_akses_asesmen === StatusAksesMenuAsesmen::BELUM_DIBERIKAN->value) {
            $messageNotif = 'Hak akses ke menu asesmen anda belum diberikan.';
        } else if ($request->status_akses_asesmen === StatusAksesMenuAsesmen::DIBERIKAN->value) {
            $messageNotif = 'Hak akses ke menu asesmen telah diberikan.';
        }

        Asesi::whereIn('id', $request->asesi_ids)->update(['status_akses_asesmen' => $request->status_akses_asesmen]);

        $asesis = Asesi::with(['student.user'])
                ->whereIn('id', $request->asesi_ids)
                ->get();

        if ($asesis->isNotEmpty()) {
            foreach ($asesis as $asesi) {
                $user = $asesi->student->user ?? null;
                if ($user) {
                    $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]);
                    $this->sendPushNotification($messaging, $user, 'Update Akses Menu Asesmen', $messageNotif, $url, 'StatusAsesiUpdated');
                }
            }
        }

        return redirect()->back()->with('message', count($request->asesi_ids) . ' asesi berhasil diperbarui hak aksesnya.');   
    }
    public function updateStatusFinalBulk(Sertification $sertification, Request $request, Messaging $messaging)
    {
        $request->validate([
            'asesi_ids' => 'required|array',
            'asesi_ids.*' => 'exists:asesis,id',
            'status_final' => ['required', Rule::in(['belum_ditetapkan', 'belum_kompeten', 'kompeten', 'diskualifikasi'])],
        ]);

        $messageNotif = '';
        if ($request->status_final === StatusFinalAsesi::KOMPETEN->value) {
            $messageNotif = 'Selamat, Anda dinyatakan Kompeten pada skema sertifikasi ini.';
        } else if ($request->status_final === StatusFinalAsesi::BELUM_KOMPETEN->value) {
            $messageNotif = 'Maaf, Anda dinyatakan Belum Kompeten pada skema sertifikasi ini.';
        } else if ($request->status_final === StatusFinalAsesi::DISKUALIFIKASI->value) {
            $messageNotif = 'Maaf, Anda dinyatakan Diskualifikasi.';
        } else if ($request->status_final === StatusFinalAsesi::BELUM_DITETAPKAN->value) {
            $messageNotif = 'Status Akhir Anda telah direset menjadi Belum Ditetapkan.';
        }

        Asesi::whereIn('id', $request->asesi_ids)->update(['status_final' => $request->status_final]);

        $asesis = Asesi::with(['student.user'])
                ->whereIn('id', $request->asesi_ids)
                ->get();

        if ($asesis->isNotEmpty()) {
            foreach ($asesis as $asesi) {
                $user = $asesi->student->user ?? null;
                if ($user) {
                    $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]);
                    $this->sendPushNotification($messaging, $user, 'Update Status Final', $messageNotif, $url, 'StatusAsesiUpdated');
                }
            }
        }

        return redirect()->back()->with('message', count($request->asesi_ids) . ' asesi berhasil diperbarui status finalnya.');   
    }

    public function updateStatusBerkasBulk(Sertification $sertification, Request $request, Messaging $messaging)
    {
        $request->validate([
            'asesi_ids' => 'required|array',
            'asesi_ids.*' => 'exists:asesis,id',
            'status_berkas' => ['required', Rule::in(['menunggu_verifikasi_admin', 'perlu_perbaikan_berkas', 'sudah_lengkap'])],
            'catatan_perbaikan' => 'nullable|string',
        ]);

        $messageNotif = '';
        if ($request->status_berkas === StatusBerkasAdministrasi::SUDAH_LENGKAP->value) {
            $messageNotif = 'Berkas Anda telah dinyatakan lengkap.';
        } else if ($request->status_berkas === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS->value) {
            $messageNotif = 'Ada berkas yang perlu anda perbaiki.';
        }
        
        Asesi::whereIn('id', $request->asesi_ids)->update([
            'status_berkas' => $request->status_berkas,
            'catatan_perbaikan' => ($request->status_berkas === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS->value) ? $request->catatan_perbaikan : null,
        ]);

        $asesis = Asesi::with(['student.user'])
            ->whereIn('id', $request->asesi_ids)
            ->get();

        foreach ($asesis as $asesi) {
            $user = $asesi->student->user;
            if ($user) {
                $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]);
                $this->sendPushNotification($messaging, $user, 'Update Status Pengajuan Asesi', $messageNotif, $url, 'StatusAsesiUpdated');
            }
        }

        return redirect()->back()->with('message', count($request->asesi_ids) . ' status berkas asesi berhasil diperbarui');
    }

    public function updateCertificate(Sertification $sertification, Asesi $asesi, Request $request, Messaging $messaging)
    {
        $sertifikat = $asesi->sertifikat()->firstOrNew(['asesi_id' => $asesi->id]);
        $validatedData = $request->validate([
            'nomor_seri' => 'nullable|string|max:255',
            'nomor_sertifikat' => 'nullable|string|max:255',
            'nomor_registrasi' => 'nullable|string|max:255',
            'tanggal_terbit' => 'required|date',
            'berlaku_hingga' => 'required|date|after_or_equal:tanggal_terbit',
            'file_path' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files', [])) && in_array('file_path', $request->input('delete_files', []));
                }),
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:2048'
            ],
            'delete_files' => 'nullable|array'
        ]);
        $sertifikat->fill(collect($validatedData)->except(['file_path', 'delete_files'])->toArray());
        FileHelper::handleSingleFileDeletes($sertifikat, $request->input('delete_files', []));
        FileHelper::handleSingleFileUploads($sertifikat, ['file_path'], $request, 'sertifikat_files');
        FileHelper::saveIfDirty([$sertifikat]);

        $user = $asesi->student->user;
        if ($user) {
            $title = 'Sertifikat Telah Terbit';
            $body = 'Selamat! Sekarang anda bisa mendownload sertifikat anda.';
            $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $body]);
            $this->sendPushNotification($messaging, $user, $title, $body, $url, 'SertifikatUploaded');
        }

        return back()->with('message', 'Sertifikat berhasil disimpan.');
    }

    public function destroyCertificate(Sertification $sertification, Asesi $asesi, Request $request)
    {
        FileHelper::handleSingleFileDeletes($asesi->sertifikat, ['file_path']);
        $asesi->sertifikat->delete();
        return back()->with('message', 'Sertifikat berhasil dihapus.');
    }
}
