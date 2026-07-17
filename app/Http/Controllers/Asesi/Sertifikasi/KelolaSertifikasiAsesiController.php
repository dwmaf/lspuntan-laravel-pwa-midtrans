<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Enums\StatusBerkasAdministrasi;
use App\Enums\StatusFinalAsesi;
use App\Http\Controllers\Controller;
use App\Traits\SendsPushNotifications;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sertifikasi;
use App\Helpers\FileHelper;
use App\Models\BerkasAsesi;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class KelolaSertifikasiAsesiController extends Controller
{
    use SendsPushNotifications;
    public function listSertifikasi(Request $request)
    {
        NotificationController::markAsRead($request);
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;
        $listAsesi = Asesi::where('mahasiswa_id', $mahasiswa->id)
            ->get()
            ->keyBy('sertifikasi_id');

        $sertifikasi_tersedia = Sertifikasi::with('skema')
            ->where('status', 'berlangsung')
            ->whereHas('skema', fn($q) => $q->where('is_active', true))
            ->orderBy('tgl_apply_dibuka', 'desc')
            ->get();

        $sertifikasi_saya = Sertifikasi::with('skema')
            ->where('status', 'selesai')
            ->whereHas('asesi', fn($q) => $q->where('mahasiswa_id', $mahasiswa->id))
            ->orderBy('tgl_apply_dibuka', 'desc')
            ->get();

        return Inertia::render('Asesi/SertifikasiList', [
            'sertifikasi_tersedia' => $sertifikasi_tersedia,
            'sertifikasi_saya' => $sertifikasi_saya,
            'listAsesi' => $listAsesi
        ]);
    }

    public function applyForm(Sertifikasi $sertifikasi, Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;
        // dd($mahasiswa);
        $existingAsesi = Asesi::where('mahasiswa_id', $mahasiswa->id)->where('sertifikasi_id', $sertifikasi->id)->first();
        if ($existingAsesi) {
            return redirect()->route('asesi.sertifikasi.applied.show', [$sertifikasi, $existingAsesi])->with('message', 'Anda sudah terdaftar pada skema sertifikasi ini.');
        }

        if ($sertifikasi->status->value === 'selesai' || $sertifikasi->status->value === 'dibatalkan') {
            return redirect()->route('asesi.sertifikasi.index')->with('error', 'Sertifikasi ini sudah selesai/dibatalkan dan tidak menerima pendaftaran baru.');
        }

        if ($sertifikasi->tgl_apply_ditutup && now()->greaterThan($sertifikasi->tgl_apply_ditutup)) {
            return redirect()->route('asesi.sertifikasi.index')->with('error', 'Masa pendaftaran sertifikasi ini sudah berakhir.');
        }
        return Inertia::render('Asesi/ApplySertifAsesi', [
            'sertifikasi' => $sertifikasi->load('skema'),
            'mahasiswa' => $mahasiswa,
            'user' => $user,
        ]);
    }

    public function submitForm(Mahasiswa $mahasiswa, Request $request)
    {
        Gate::authorize('update', $mahasiswa);
        // dd($request);
        $request->validate([
            'sertifikasi_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'tmpt_lhr' => 'required|string|max:255',
            'tgl_lhr' => 'required|string|max:255',
            'kelamin' => 'required|string|max:255',
            'kebangsaan' => 'required|string|max:255',
            'no_tlp_hp' => 'required|string|max:255',
            'kualifikasi_pendidikan' => 'required|string|max:255',
            'tujuan_sert' => 'required|string|max:255',
            'rekap_nilai' => 'required|string|max:255',
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'apl_1' => 'required|file|mimes:docx|max:2048',
            'apl_2' => 'required|file|mimes:docx|max:2048',
            'transkrip_nilai' => 'required|file|mimes:pdf|max:2048',
            'foto_ktm' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_ktp' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files', [])) && in_array('foto_ktp', $request->input('delete_files', []));
                }),
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],
            'pas_foto' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files', [])) && in_array('pas_foto', $request->input('delete_files', []));
                }),
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],
            'surat_ket_magang' => 'nullable|array|max:5',
            'surat_ket_magang.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'sertif_pelatihan' => 'nullable|array|max:5',
            'sertif_pelatihan.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'dok_pendukung_lain' => 'nullable|array|max:5',
            'dok_pendukung_lain.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
            'delete_files' => 'nullable|array',
        ]);
        $sertifikasi = Sertifikasi::findOrFail($request->sertifikasi_id);
        if ($sertifikasi->status->value === 'selesai' || $sertifikasi->status->value === 'dibatalkan') {
            return redirect()->route('asesi.sertifikasi.index')->with('error', 'Sertifikasi ini sudah selesai/dibatalkan dan tidak menerima pendaftaran baru.');
        }

        if ($sertifikasi->tgl_apply_ditutup && now()->greaterThan($sertifikasi->tgl_apply_ditutup)) {
            return redirect()->route('asesi.sertifikasi.index')->with('error', 'Masa pendaftaran sertifikasi ini sudah berakhir.');
        }

        $asesi = DB::transaction(function () use ($request, $mahasiswa) {
            $user = $mahasiswa->user;
            $mahasiswa->fill($request->only(['nik', 'tmpt_lhr', 'tgl_lhr', 'kelamin', 'kebangsaan', 'no_tlp_rmh', 'no_tlp_kntr', 'kualifikasi_pendidikan',]));
            $user->fill($request->only(['no_tlp_hp', 'name']));
            FileHelper::handleSingleFileDeletes($mahasiswa, $request->input('delete_files', []));
            FileHelper::handleSingleFileUploads($mahasiswa, ['foto_ktp', 'pas_foto'], $request, 'berkas_mahasiswa');
            FileHelper::saveIfDirty([$mahasiswa, $user]);

            $asesi = new Asesi($request->only(['sertifikasi_id', 'tujuan_sert', 'rekap_nilai']));
            $asesi->mahasiswa_id = $mahasiswa->id;
            FileHelper::handleSingleFileUploads($asesi, ['bukti_bayar', 'apl_1', 'apl_2', 'foto_ktm', 'transkrip_nilai'], $request, 'berkas_asesi');
            $asesi->save();
            FileHelper::handleCollectionFileUploads(BerkasAsesi::class, 'asesi_id', $asesi->id, $request, ['surat_ket_magang', 'sertif_pelatihan', 'dok_pendukung_lain'], 'berkas_asesi');

            return $asesi;
        });
        $asesiForNotif = Asesi::with('mahasiswa.user', 'sertifikasi.skema', 'sertifikasi.asesor.user')->findOrFail($asesi->id);
        $sertifikasi = $asesiForNotif->sertifikasi;
        $user = $asesiForNotif->mahasiswa->user;

        // Kirim push notif ke Semua Admin
        $recipients = User::role('admin')->get();
        if ($recipients->isNotEmpty()) {
            $title = 'Pendaftar Baru';
            $body = $user->name . ' telah mendaftar sertifikasi ' . $sertifikasi->skema->nama_skema;
            $url = route('admin.sertifikasi.pendaftar.show', [$sertifikasi, $asesiForNotif]);
            foreach ($recipients as $recipient) {
                $this->sendPushNotification($recipient, $title, $body, $url, 'PendaftarBaru');
            }
        }
        return redirect(route('asesi.sertifikasi.applied.show', [$asesi->sertifikasi_id, $asesi]))->with('message', 'Berhasil daftar sertifikasi');
    }

    public function showApplied(Sertifikasi $sertifikasi, Asesi $asesi, Request $request)
    {
        Gate::authorize('view', $asesi);
        NotificationController::markAsRead($request);
        $asesi->load([
            'mahasiswa.user',
            'berkasAsesi',
            'asesor.user',
            'sertifikat'
        ]);
        $mahasiswa = $asesi->mahasiswa;
        return Inertia::render('Asesi/DetailSertifAsesi', [
            'sertifikasi' => $sertifikasi->load('skema'),
            'asesi' => $asesi,
            'mahasiswa' => $mahasiswa,
            'statusBerkasAdministrasiOptions' => StatusBerkasAdministrasi::options(),
            'StatusFinalAsesiOptions' => StatusFinalAsesi::options(),
        ]);
    }

    public function updateApplied(Sertifikasi $sertifikasi, Asesi $asesi, Request $request)
    {
        // dd($request);
        if ($asesi->status_berkas === StatusBerkasAdministrasi::SUDAH_LENGKAP->value) {
            abort(403, 'Data tidak dapat diubah karena berkas sudah diverifikasi dan dinyatakan lengkap.');
        }

        Gate::authorize('update', $asesi);
        $asesi->load('mahasiswa.user', 'berkasAsesi');
        
        $mahasiswa = $asesi->mahasiswa;
        $user = $mahasiswa->user;
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'tmpt_lhr' => 'required|string|max:255',
            'tgl_lhr' => 'required|string|max:255',
            'kelamin' => 'required|string|max:255',
            'kebangsaan' => 'required|string|max:255',
            'no_tlp_hp' => 'required|string|max:255',
            'kualifikasi_pendidikan' => 'required|string|max:255',
            'tujuan_sert' => 'required|string|max:255',
            'rekap_nilai' => 'required|string|max:255',
            'bukti_bayar' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('bukti_bayar', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:png,jpg,jpeg,pdf',
                'max:2048'
            ],
            'apl_1' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('apl_1', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'apl_2' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('apl_2', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:docx',
                'max:2048'
            ],
            'transkrip_nilai' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('trnaskrip_nilai', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:pdf',
                'max:2048'
            ],
            'foto_ktp' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_mahasiswa', [])) && in_array('foto_ktp', $request->input('delete_files_mahasiswa', []));
                }),
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],
            'foto_ktm' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('foto_ktm', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],
            'pas_foto' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_mahasiswa', [])) && in_array('pas_foto', $request->input('delete_files_mahasiswa', []));
                }),
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],

            // 'kartu_hasil_studi' => [
            //     function ($attribute, $value, $fail) use ($request, $asesi) {
            //         $existingFilesCount = $asesi->asesifiles()->where('type', 'kartu_hasil_studi')->count();
            //         $deleteFilesCount = 0;
            //         if ($request->filled('delete_files_collection')) {
            //             $deleteFilesCount = $asesi->asesifiles()
            //                 ->where('type', 'kartu_hasil_studi')->whereIn('id', $request->delete_files_collection)->count();
            //         }
            //         if (empty($value) && $existingFilesCount > 0 && $existingFilesCount === $deleteFilesCount) {
            //             $fail('Field kartu hasil studi wajib diisi.');
            //         }
            //     },
            //     'nullable',
            //     'array',
            //     'max:5'
            // ],
            'surat_ket_magang.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'sertif_pelatihan' => 'nullable|array|max:5',
            'sertif_pelatihan.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'dok_pendukung_lain' => 'nullable|array|max:5',
            'dok_pendukung_lain.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'delete_files_collection' => 'nullable|array',
            'delete_files_collection.*' => 'integer|exists:berkas_asesi,id',
            'delete_files_mahasiswa' => 'nullable|array',
            'delete_files_asesi' => 'nullable|array',
        ]);
        $shoulSendNotif = false;
        DB::transaction(function () use ($request, $mahasiswa, $asesi, $user, &$shoulSendNotif) {
            $initialStatus = $asesi->status_berkas;
            $mahasiswa->fill($request->only(['nik', 'tmpt_lhr', 'tgl_lhr', 'kelamin', 'kebangsaan', 'no_tlp_rmh', 'no_tlp_kntr', 'kualifikasi_pendidikan',]));
            $user->fill($request->only(['no_tlp_hp', 'name']));
            $asesi->fill($request->only(['tujuan_sert', 'rekap_nilai']));
            FileHelper::handleSingleFileDeletes($mahasiswa, $request->input('delete_files_mahasiswa', []));
            FileHelper::handleSingleFileDeletes($asesi, $request->input('delete_files_asesi', []));
            FileHelper::handleCollectionFileDeletes(BerkasAsesi::class, $request->input('delete_files_collection', []));

            FileHelper::handleSingleFileUploads($mahasiswa, ['foto_ktp', 'pas_foto'], $request, 'berkas_mahasiswa');
            FileHelper::handleSingleFileUploads($asesi, ['bukti_bayar', 'apl_1', 'apl_2', 'foto_ktm', 'transkrip_nilai'], $request, 'berkas_asesi');
            FileHelper::handleCollectionFileUploads(BerkasAsesi::class, 'asesi_id', $asesi->id, $request, ['surat_ket_magang', 'sertif_pelatihan', 'dok_pendukung_lain'], 'asesi_files');

            FileHelper::saveIfDirty([$mahasiswa, $user, $asesi]);

            if ($initialStatus === StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS) {
                $asesi->status_berkas = StatusBerkasAdministrasi::MENUNGGU_VERIFIKASI_ADMIN;
                $asesi->catatan_perbaikan = null;
                $asesi->save();
                $shoulSendNotif = true;
            }
        });

        if ($shoulSendNotif) {
            $asesi->load('mahasiswa.user', 'sertifikasi.skema', 'sertifikasi.asesor.user');
            $sertifikasi = $asesi->sertifikasi;
            $user = $asesi->mahasiswa->user;

            // Kirim notif ke Admin kalau status awalnya adalah perlu_perbaikan_berkas
            $recipients = User::role('admin')->get();

            if ($recipients->isNotEmpty()) {
                $title = 'Berkas Diperbaiki';
                $body = $user->name . ' telah memperbaiki dan mengirim ulang berkas untuk sertifikasi ' . $sertifikasi->skema->nama_skema;
                $url = route('admin.sertifikasi.pendaftar.show', [$sertifikasi, $asesi]);

                foreach ($recipients as $recipient) {
                    $this->sendPushNotification($recipient, $title, $body, $url, 'BerkasDiperbaiki');
                }
            }
        }
        return redirect()->back()->with('message', 'Berhasil update data Anda');
    }
}
