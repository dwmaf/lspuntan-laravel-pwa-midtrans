<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Enums\StatusBerkasAdministrasi;
use App\Enums\StatusFinalAsesi;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Traits\SendsPushNotifications;
use App\Traits\AuthorizesBulkActions;
use App\Helpers\FileHelper;
use Inertia\Inertia;
use Kreait\Firebase\Contract\Messaging;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class PendaftarController extends Controller
{
    use SendsPushNotifications;
    use AuthorizesBulkActions;

    public function listAsesi(Sertification $sertification, Request $request)
    {
        // Authorization: Admin bisa lihat semua, Asesor hanya asesi yang mereka ampu
        Gate::authorize('view', $sertification);

        $sertification->load('skema', 'asesors.user');
        $user = $request->user();
        $unassignedCount = 0;

        if ($user->hasRole('admin')) {
            $sertification->load('asesis.student.user', 'asesis.asesor.user');
        } else if ($user->hasRole('asesor')) {
            $asesorId = $user->asesor?->id;
            $sertification->load(['asesis' => function ($query) use ($asesorId) {
                $query->where('asesor_id', $asesorId);
            }, 'asesis.student.user', 'asesis.asesor.user']);

            $unassignedCount = Asesi::where('sertification_id', $sertification->id)
                ->whereNull('asesor_id')
                ->count();
        }

        return Inertia::render('Admin/PendaftarList', [
            'sertification' => $sertification,
            'unassignedCount' => $unassignedCount,
        ]);
    }

    public function rincianDataAsesi(Sertification $sertification, Asesi $asesi, Request $request)
    {
        Gate::authorize('view', $asesi);
        NotificationController::markAsRead($request);
        $sertification->load('skema', 'asesors.user');
        $asesi->load(['student.user', 'asesifiles', 'sertifikat', 'asesor.user']);

        return Inertia::render('Admin/PendaftarDetail', [
            'asesi' => $asesi,
            'sertification' => $sertification,
            'statusBerkasAdministrasiOptions' => StatusBerkasAdministrasi::options(),
            'StatusFinalAsesiOptions' => StatusFinalAsesi::options(),
            'canManageCertificate' => Gate::allows('manageCertificate', $asesi),
        ]);
    }

    public function updateStatusBerkas(Sertification $sertification, Asesi $asesi, Request $request, Messaging $messaging)
    {
        Gate::authorize('update', $asesi);
        if ($asesi->status_final->value !== StatusFinalAsesi::BELUM_DITETAPKAN->value) {
            return redirect()->back()->with('error', 'Gagal! Status berkas sudah terkunci karena status akhir asesi telah ditetapkan.');
        }
        if (!is_null($asesi->asesor_id)) {
            return redirect()->back()->with('error', 'Gagal! Status berkas sudah terkunci karena asesor telah ditetapkan.');
        }
        $messageNotif = '';
        if ($request->status_berkas === StatusBerkasAdministrasi::SUDAH_LENGKAP->value) {
            $messageNotif = 'Berkas Anda telah dinyatakan lengkap.';
        } else if ($request->status_berkas === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS->value) {
            $messageNotif = 'Ada berkas yang perlu anda perbaiki.';
        } else if ($request->status_berkas === StatusBerkasAdministrasi::MENUNGGU_VERIFIKASI_ADMIN->value) {
            $messageNotif = 'Berkas Anda sedang dalam antrean untuk diverifikasi oleh Admin LSP.';
        }

        $asesi->update([
            'status_berkas' => $request->status_berkas,
            'catatan_perbaikan' => ($request->status_berkas === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS->value) ? $request->catatan_perbaikan : null,
        ]);

        $user = $asesi->student->user;
        if ($user) $this->sendPushNotification($messaging, $user, 'Update Status Pengajuan Asesi', $messageNotif, route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]), 'StatusAsesiUpdated');
        return redirect()->back()->with('message', 'Status asesi berhasil diperbarui');
    }

    public function updateStatusFinal(Sertification $sertification, Asesi $asesi, Request $request, Messaging $messaging)
    {
        Gate::authorize('updateStatusFinal', $asesi);
        if ($asesi->sertifikat()->exists()) {
            return redirect()->back()->with('error', 'Gagal! Status akhir tidak dapat diubah karena sertifikat telah diterbitkan.');
        }
        if (is_null($asesi->asesor_id)) {
            return redirect()->back()->with('error', 'Gagal! Anda belum bisa menetapkan status akhir karena asesor belum ditetapkan.');
        }
        if (
            $request->status_final !== StatusFinalAsesi::BELUM_DITETAPKAN->value &&
            $asesi->status_berkas->value !== StatusBerkasAdministrasi::SUDAH_LENGKAP->value
        ) {
            return redirect()->back()->with('error', 'Gagal! Anda belum bisa menetapkan status akhir karena berkas asesi belum berstatus Sudah Lengkap.');
        }
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

    public function assignAsesor(Sertification $sertification, Asesi $asesi, Request $request)
    {
        Gate::authorize('assignAsesor', $asesi);

        if ($asesi->status_final->value !== StatusFinalAsesi::BELUM_DITETAPKAN->value) {
            return back()->with('error', 'Gagal! Asesor tidak dapat diubah karena status akhir telah ditetapkan.');
        }

        $request->validate(['asesor_id' => 'required|exists:asesors,id']);
        if (!$sertification->asesors->contains($request->asesor_id)) {
            return back()->with('error', 'Asesor tidak terdatar di sertifikasi ini.');
        }

        $asesi->update(['asesor_id' => $request->asesor_id]);
        return back()->with('message', 'Asesor berhasil ditetapkan.');
    }

    public function assignAsesorBulk(Sertification $sertification, Request $request)
    {
        $request->validate([
            'asesi_ids' => 'required|array',
            'asesi_ids.*' => 'exists:asesis,id',
            'asesor_id' => 'required|exists:asesors,id',
        ]);
        if (!$sertification->asesors->contains($request->asesor_id)) {
            return back()->with('error', 'Asesor tidak terdatar di sertifikasi ini.');
        }

        $asesis = Asesi::whereIn('id', $request->asesi_ids)->get();
        $this->authorizeBulk('assignAsesor', $asesis);
        foreach ($asesis as $asesi) {
            if ($asesi->status_berkas->value !== StatusBerkasAdministrasi::SUDAH_LENGKAP->value) {
                return redirect()->back()->with('error', 'Gagal: Salah satu asesi belum memiliki berkas yang lengkap.');
            }
            if ($asesi->status_final->value !== StatusFinalAsesi::BELUM_DITETAPKAN->value) {
                return redirect()->back()->with('error', 'Gagal: Salah satu asesi sudah memiliki status final yang ditetapkan.');
            }
        }

        Asesi::whereIn('id', $request->asesi_ids)->update(['asesor_id' => $request->asesor_id]);
        return redirect()->back()->with('message', count($request->asesi_ids) . ' asesi berhasil di-assign ke asesor.');
    }

    public function updateStatusFinalBulk(Sertification $sertification, Request $request, Messaging $messaging)
    {
        $request->validate([
            'asesi_ids' => 'required|array',
            'asesi_ids.*' => 'exists:asesis,id',
            'status_final' => ['required', Rule::in(['belum_ditetapkan', 'belum_kompeten', 'kompeten', 'diskualifikasi'])],
        ]);

        // Authorization: Cek apakah user bisa update semua asesi yang dipilih
        $asesis = Asesi::with(['student.user', 'asesor', 'sertifikat'])->whereIn('id', $request->asesi_ids)->get();
        $this->authorizeBulk('updateStatusFinal', $asesis);
        foreach ($asesis as $asesi) {
            if ($asesi->sertifikat) {
                return redirect()->back()->with('error', 'Gagal: Salah satu asesi sudah diterbitkan sertifikatnya.');
            }
            if (is_null($asesi->asesor_id)) {
                return redirect()->back()->with('error', 'Gagal: Salah satu asesi belum ditetapkan asesornya.');
            }
            if ($asesi->status_berkas->value !== StatusBerkasAdministrasi::SUDAH_LENGKAP->value) {
                return redirect()->back()->with('error', 'Gagal: Salah satu asesi belum berstatus berkas Sudah Lengkap.');
            }
        }
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

        // Authorization: Cek apakah user bisa update semua asesi yang dipilih
        $asesis = Asesi::whereIn('id', $request->asesi_ids)->get();
        $this->authorizeBulk('update', $asesis);

        $messageNotif = '';
        if ($request->status_berkas === StatusBerkasAdministrasi::SUDAH_LENGKAP->value) {
            $messageNotif = 'Berkas Anda telah dinyatakan lengkap.';
        } else if ($request->status_berkas === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS->value) {
            $messageNotif = 'Ada berkas yang perlu anda perbaiki.';
        } else if ($request->status_berkas === StatusBerkasAdministrasi::MENUNGGU_VERIFIKASI_ADMIN->value) {
            $messageNotif = 'Berkas Anda sedang dalam antrean untuk diverifikasi oleh Admin LSP.';
        }

        Asesi::whereIn('id', $request->asesi_ids)->update([
            'status_berkas' => $request->status_berkas,
            'catatan_perbaikan' => ($request->status_berkas === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS->value) ? $request->catatan_perbaikan : null,
        ]);

        $asesis = Asesi::with(['student.user'])
            ->whereIn('id', $request->asesi_ids)
            ->get();

        foreach ($asesis as $asesi) {
            if ($asesi->asesor_id !== null || $asesi->status_final->value !== StatusFinalAsesi::BELUM_DITETAPKAN->value) {
                return redirect()->back()->with('error', 'Salah satu asesi yang dipilih sudah memiliki asesor atau status final.');
            }
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
        // Authorization: Hanya admin yang bisa manage sertifikat
        Gate::authorize('manageCertificate', $asesi);

        $sertifikat = $asesi->sertifikat()->firstOrNew(['asesi_id' => $asesi->id]);
        $validatedData = $request->validate([
            'nomor_seri' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sertifikats', 'nomor_seri')->ignore($sertifikat->id),
            ],
            'nomor_sertifikat' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sertifikats', 'nomor_sertifikat')->ignore($sertifikat->id),
            ],
            'nomor_registrasi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sertifikats', 'nomor_registrasi')->ignore($sertifikat->id),
            ],
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
        // Authorization: Hanya admin yang bisa manage sertifikat
        Gate::authorize('manageCertificate', $asesi);

        FileHelper::handleSingleFileDeletes($asesi->sertifikat, ['file_path']);
        $asesi->sertifikat->delete();
        return back()->with('message', 'Sertifikat berhasil dihapus.');
    }
}
