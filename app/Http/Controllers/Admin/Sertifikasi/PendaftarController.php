<?php

namespace App\Http\Controllers\Admin\Sertifikasi;

use App\Enums\StatusBerkasAdministrasi;
use App\Enums\StatusFinalAsesi;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Models\Asesor;
use App\Traits\SendsPushNotifications;
use App\Traits\AuthorizesBulkActions;
use App\Helpers\FileHelper;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class PendaftarController extends Controller
{
    use SendsPushNotifications;
    use AuthorizesBulkActions;

    public function listAsesi(Sertification $sertification, Request $request)
    {
        // Authorization: Admin bisa lihat semua, Asesor hanya asesi yang mereka ampu
        Gate::authorize('view', $sertification);
        NotificationController::markAsRead($request);

        $sertification->load('skema', 'asesors.user');
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $sertification->load('asesis.student.user', 'asesis.asesor.user', 'asesis.sertifikat');
        } else if ($user->hasRole('asesor')) {
            $asesorId = $user->asesor?->id;
            $sertification->load(['asesis' => function ($query) use ($asesorId) {
                $query->where('asesor_id', $asesorId);
            }, 'asesis.student.user', 'asesis.asesor.user', 'asesis.sertifikat']);
        }

        $unassignedCount = Asesi::where('sertification_id', $sertification->id)
            ->whereNull('asesor_id')
            ->count();

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

    public function updateStatusBerkas(Sertification $sertification, Asesi $asesi, Request $request)
    {
        Gate::authorize('update', $asesi);
        if ($asesi->status_final->value !== StatusFinalAsesi::BELUM_DITETAPKAN->value) {
            return redirect()->back()->with('error', 'Gagal! Status berkas sudah terkunci karena status akhir asesi telah ditetapkan.');
        }
        if (!is_null($asesi->asesor_id)) {
            return redirect()->back()->with('error', 'Gagal! Status berkas sudah terkunci karena asesor telah ditetapkan.');
        }

        $messageNotif = match ($request->status_berkas) {
            StatusBerkasAdministrasi::SUDAH_LENGKAP->value => 'Berkas Anda telah dinyatakan lengkap.',
            StatusBerkasAdministrasi::MENUNGGU_VERIFIKASI_ADMIN->value => 'Berkas Anda sedang dalam antrean untuk diverifikasi oleh Admin LSP.',
            default => '',
        };

        $asesi->update([
            'status_berkas' => $request->status_berkas,
            'catatan_perbaikan' => ($request->status_berkas === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS->value) ? $request->catatan_perbaikan : null,
        ]);

        $user = $asesi->student->user;
        // kirim notif ke asesi yang berkasnya diperbarui
        if ($user) $this->sendPushNotification($user, 'Update Status Pengajuan Asesi', $messageNotif, route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]), 'StatusAsesiUpdated');
        return redirect()->back()->with('message', 'Status asesi berhasil diperbarui');
    }

    public function updateStatusFinal(Sertification $sertification, Asesi $asesi, Request $request)
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
        $messageNotif = match ($request->status_berkas) {
            StatusFinalAsesi::KOMPETEN->value => 'Selamat, Anda dinyatakan Kompeten pada skema sertifikasi ini.',
            StatusFinalAsesi::BELUM_KOMPETEN->value => 'Maaf, Anda dinyatakan Belum Kompeten pada skema sertifikasi ini.',
            StatusFinalAsesi::DISKUALIFIKASI->value => 'Maaf, Anda dinyatakan Diskualifikasi.',
            StatusFinalAsesi::BELUM_DITETAPKAN->value => 'Status Akhir Anda telah direset menjadi Belum Ditetapkan.',
            default => '',
        };

        $asesi->update(['status_final' => $request->status_final]);

        // kirim push notif ke asesi yang status finalnya diperbarui
        $user = $asesi->student->user;
        if ($user) {
            $title = 'Update Status Final';
            $body = $messageNotif;
            $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]);
            $this->sendPushNotification($user, $title, $body, $url, 'StatusAsesiUpdated');
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
        $asesor = Asesor::with('user')->findOrFail($request->asesor_id);
        $asesiUser = $asesi->student->user;

        $messageNotif = $asesor->user->name . ' ditetapkan sebagai Asesor Anda.';
        // kirim push notif ke asesi yang ditetapkan asesornya
        if ($asesiUser) {
            $title = 'Penetapan Asesor';
            $body = $messageNotif;
            $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]);
            $this->sendPushNotification($asesiUser, $title, $body, $url, 'StatusAsesiUpdated');
        }

        $asesorUser = $asesor->user;
        // kirim push notif ke asesor yang ditugaskan
        if ($asesorUser) {
            $title = 'Penugasan Asesor';
            $body = 'Anda ditetapkan sebagai asesor untuk ' . $asesiUser->name . ' pada sertifikasi ' . $sertification->skema->nama_skema . '.';
            $url = route('admin.sertifikasi.pendaftar.show', [$sertification, $asesi]);
            $this->sendPushNotification($asesorUser, $title, $body, $url, 'AsesorDitugaskan');
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

        $asesor = Asesor::with('user')->find($request->asesor_id);

        $asesiNames = Asesi::with('student.user')->whereIn('id', $request->asesi_ids)->get()
            ->map(fn($a) => $a->student?->user?->name ?? "Asesi #{$a->id}")
            ->values()
            ->toArray();

        activity()
            ->performedOn($sertification)
            ->causedBy(Auth::user())
            ->withProperties([
                'asesi_ids' => $request->asesi_ids,
                'asesi_names' => $asesiNames,
                'asesor_id' => $request->asesor_id,
                'asesor_name' => $asesor->user->name,
            ])
            ->event('updated')
            ->log("menetapkan Asesor {$asesor->user->name} ke " . count($request->asesi_ids) . " asesi");

        // kirim push notif ke asesor yg ditugaskan untuk n jumlah asesi
        if ($asesor && $asesor->user) {
            $title = 'Penugasan Asesor';
            $body = 'Anda ditetapkan sebagai asesor untuk ' . count($request->asesi_ids) . ' asesi pada sertifikasi ' . $sertification->skema->nama_skema . '.';
            $url = route('admin.sertifikasi.pendaftar.index', $sertification);
            $this->sendPushNotification($asesor->user, $title, $body, $url, 'AsesorDitugaskan');
        }

        $asesis = Asesi::with('student.user')->whereIn('id', $request->asesi_ids)->get();
        // kirim push notif ke para asesi yang ditetapkan asesornya
        foreach ($asesis as $asesi) {
            $asesiUser = $asesi->student->user;
            if ($asesiUser) {
                $title = 'Penetapan Asesor';
                $body = $asesor->user->name . ' ditetapkan sebagai Asesor Anda.';
                $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $body]);
                $this->sendPushNotification($asesiUser, $title, $body, $url, 'StatusAsesiUpdated');
            }
        }

        return redirect()->back()->with('message', count($request->asesi_ids) . ' asesi berhasil di-assign ke asesor.');
    }

    public function updateStatusFinalBulk(Sertification $sertification, Request $request)
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
        $messageNotif = match ($request->status_berkas) {
            StatusFinalAsesi::KOMPETEN->value => 'Selamat, Anda dinyatakan Kompeten pada skema sertifikasi ini.',
            StatusFinalAsesi::BELUM_KOMPETEN->value => 'Maaf, Anda dinyatakan Belum Kompeten pada skema sertifikasi ini.',
            StatusFinalAsesi::DISKUALIFIKASI->value => 'Maaf, Anda dinyatakan Diskualifikasi.',
            StatusFinalAsesi::BELUM_DITETAPKAN->value => 'Status Akhir Anda telah direset menjadi Belum Ditetapkan.',
            default => '',
        };

        $oldFinalStatuses = Asesi::whereIn('id', $request->asesi_ids)->pluck('status_final', 'id');
        $asesiNames = Asesi::with('student.user')->whereIn('id', $request->asesi_ids)->get()
            ->map(fn($a) => $a->student?->user?->name ?? "Asesi #{$a->id}")
            ->values()
            ->toArray();

        Asesi::whereIn('id', $request->asesi_ids)->update(['status_final' => $request->status_final]);

        activity()
            ->performedOn($sertification)
            ->causedBy(Auth::user())
            ->withProperties([
                'asesi_ids' => $request->asesi_ids,
                'asesi_names' => $asesiNames,
                'old' => ['status_final' => $oldFinalStatuses],
                'attributes' => ['status_final' => $request->status_final],
            ])
            ->event('updated')
            ->log("mengubah status final " . count($request->asesi_ids) . " asesi menjadi {$request->status_final}");

        $asesis = Asesi::with(['student.user'])
            ->whereIn('id', $request->asesi_ids)
            ->get();

        // kirim push notif ke semua asesi yg ditetapkan status finalnya
        if ($asesis->isNotEmpty()) {
            foreach ($asesis as $asesi) {
                $user = $asesi->student->user ?? null;
                if ($user) {
                    $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]);
                    $this->sendPushNotification($user, 'Update Status Final', $messageNotif, $url, 'StatusAsesiUpdated');
                }
            }
        }

        return redirect()->back()->with('message', count($request->asesi_ids) . ' asesi berhasil diperbarui status finalnya.');
    }

    public function updateStatusBerkasBulk(Sertification $sertification, Request $request)
    {
        $request->validate([
            'asesi_ids' => 'required|array',
            'asesi_ids.*' => 'exists:asesis,id',
            'status_berkas' => ['required', Rule::in(['menunggu_verifikasi_admin', 'perlu_perbaikan_berkas', 'sudah_lengkap'])],
            'catatan_perbaikan' => 'nullable|string',
        ]);

        $asesis = Asesi::with(['student.user'])
            ->whereIn('id', $request->asesi_ids)
            ->get();
        
        // pengecekan siapa yg boleh ubah status_berkas asesi, yaitu hanya Admin
        $this->authorizeBulk('update', $asesis);

        foreach ($asesis as $asesi) {
            if ($asesi->asesor_id !== null || $asesi->status_final->value !== StatusFinalAsesi::BELUM_DITETAPKAN->value) {
                return redirect()->back()->with('error', "Asesi {$asesi->id} tidak dapat diubah karena sudah memiliki asesor atau status final telah ditetapkan.");
            }
        }

        $messageNotif = match ($request->status_berkas) {
            StatusBerkasAdministrasi::SUDAH_LENGKAP->value => 'Berkas Anda telah dinyatakan lengkap.',
            StatusBerkasAdministrasi::MENUNGGU_VERIFIKASI_ADMIN->value => 'Berkas Anda sedang dalam antrean untuk diverifikasi oleh Admin LSP.',
            default => '',
        };

        $oldBerkasStatuses = Asesi::whereIn('id', $request->asesi_ids)->pluck('status_berkas', 'id');
        $asesiNames = Asesi::with('student.user')->whereIn('id', $request->asesi_ids)->get()
            ->map(fn($a) => $a->student?->user?->name ?? "Asesi #{$a->id}")
            ->values()
            ->toArray();

        Asesi::whereIn('id', $request->asesi_ids)->update([
            'status_berkas' => $request->status_berkas,
            'catatan_perbaikan' => ($request->status_berkas === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS->value) ? $request->catatan_perbaikan : null,
        ]);

        activity()
            ->performedOn($sertification)
            ->causedBy(Auth::user())
            ->withProperties([
                'asesi_ids' => $request->asesi_ids,
                'asesi_names' => $asesiNames,
                'old' => ['status_berkas' => $oldBerkasStatuses],
                'attributes' => ['status_berkas' => $request->status_berkas],
            ])
            ->event('updated')
            ->log("mengubah status berkas " . count($request->asesi_ids) . " asesi menjadi {$request->status_berkas}");

        foreach ($asesis as $asesi) {
            $user = $asesi->student->user;
            if ($user) {
                $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $messageNotif]);
                $this->sendPushNotification($user, 'Update Status Pengajuan Asesi', $messageNotif, $url, 'StatusAsesiUpdated');
            }
        }

        return redirect()->back()->with('message', count($request->asesi_ids) . ' status berkas asesi berhasil diperbarui');
    }

    public function updateCertificate(Sertification $sertification, Asesi $asesi, Request $request)
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
        // kirim notif ke asesi yang diberikan sertifikat
        if ($user) {
            $title = 'Sertifikat Telah Terbit';
            $body = 'Selamat! Sekarang anda bisa mendownload sertifikat anda.';
            $url = route('asesi.sertifikasi.applied.show', [$sertification, $asesi, 'messageNotif' => $body]);
            $this->sendPushNotification($user, $title, $body, $url, 'SertifikatUploaded');
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
