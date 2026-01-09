<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Enums\StatusAksesMenuAsesmen;
use App\Enums\StatusBerkasAdministrasi;
use App\Enums\StatusFinalAsesi;
use App\Http\Controllers\Controller;
use App\Traits\SendsPushNotifications;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\Student;
use App\Models\User;
use App\Models\Makulnilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sertification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use App\Models\Asesifile;
use Kreait\Firebase\Contract\Messaging;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class KelolaSertifikasiAsesiController extends Controller
{
    use SendsPushNotifications;
    public function listSertifications(Request $request)
    {
        NotificationController::markAsRead($request);
        $user = $request->user();
        $student = $user->student;
        $asesis = Asesi::where('student_id', $student->id)
            ->get()
            ->keyBy('sertification_id');

        $sertifications_tersedia = Sertification::with('skema')
            ->where('status', 'berlangsung')
            ->whereHas('skema', fn($q) => $q->where('is_active', true))
            ->orderBy('tgl_apply_dibuka', 'desc')
            ->get();

        $sertifications_saya = Sertification::with('skema')
            ->whereHas('asesis', fn($q) => $q->where('student_id', $student->id))
            ->orderBy('tgl_apply_dibuka', 'desc')
            ->get();

        return Inertia::render('Asesi/SertifikasiList', [
            'sertifications_tersedia' => $sertifications_tersedia,
            'sertifications_saya' => $sertifications_saya,
            'asesis' => $asesis
        ]);
    }

    public function applyForm(Sertification $sertification, Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        // dd($student);
        $existingAsesi = Asesi::where('student_id', $student->id)->where('sertification_id', $sertification->id)->first();
        if ($existingAsesi) {
            return redirect()->route('asesi.sertifikasi.applied.show', [$sertification, $existingAsesi])->with('message', 'Anda sudah terdaftar pada skema sertifikasi ini.');
        }

        if (!$sertification->skema->is_active) {
            return redirect()->route('asesi.sertifikasi.list')->with('error', 'Skema sertifikasi ini sudah tidak aktif dan tidak menerima pendaftaran baru.');
        }
        return Inertia::render('Asesi/ApplySertifAsesi', [
            'sertification' => $sertification->load('skema'),
            'student' => $student,
            'user' => $user,
        ]);
    }

    public function submitForm(Student $student, Request $request, Messaging $messaging)
    {
        // dd($request);
        $request->validate([
            'sertification_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'tmpt_lhr' => 'required|string|max:255',
            'tgl_lhr' => 'required|string|max:255',
            'kelamin' => 'required|string|max:255',
            'kebangsaan' => 'required|string|max:255',
            'no_tlp_hp' => 'required|string|max:255',
            'kualifikasi_pendidikan' => 'required|string|max:255',
            'tujuan_sert' => 'required|string|max:255',

            'makulNilais' => 'required|array|min:1',
            'makulNilais.*.nama_makul' => 'required|string|max:255',
            'makulNilais.*.nilai_makul' => 'required|string|max:10',
            'apl_1' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'apl_2' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
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

            'kartu_hasil_studi' => 'required|array|max:5',
            'kartu_hasil_studi.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'surat_ket_magang' => 'nullable|array|max:5',
            'surat_ket_magang.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'sertif_pelatihan' => 'nullable|array|max:5',
            'sertif_pelatihan.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'dok_pendukung_lain' => 'nullable|array|max:5',
            'dok_pendukung_lain.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'delete_files' => 'nullable|array',
        ]);
        $sertification = Sertification::findOrFail($request->sertification_id);
        if (!$sertification->skema->is_active) {
            return redirect()->route('asesi.sertifikasi.list')->with('error', 'Skema sertifikasi ini sudah tidak aktif dan tidak menerima pendaftaran baru.');
        }

        $asesi = DB::transaction(function () use ($request, $student, $messaging) {
            $user = $student->user;
            $student->fill($request->only(['nik','tmpt_lhr','tgl_lhr','kelamin','kebangsaan','no_tlp_rmh','no_tlp_kntr','kualifikasi_pendidikan',]));
            $user->fill($request->only(['no_tlp_hp', 'name']));
            FileHelper::handleSingleFileDeletes($student, $request->input('delete_files', []));
            FileHelper::handleSingleFileUploads($student, ['foto_ktp', 'pas_foto'], $request, 'student_files');
            FileHelper::saveIfDirty([$student, $user]);
            
            $asesi = new Asesi($request->only(['sertification_id', 'tujuan_sert']));
            $asesi->student_id = $student->id;
            FileHelper::handleSingleFileUploads($asesi, ['apl_1', 'apl_2', 'foto_ktm'], $request, 'asesi_files');
            $asesi->save();
            FileHelper::handleCollectionFileUploads(Asesifile::class,'asesi_id',$asesi->id, $request,['surat_ket_magang', 'sertif_pelatihan', 'dok_pendukung_lain', 'kartu_hasil_studi'], 'asesi_files');
            foreach ($request->makulNilais as $makul) {
                MakulNilai::create(['asesi_id' => $asesi->id,'nama_makul' => $makul['nama_makul'],'nilai_makul' => $makul['nilai_makul'],]);
            }

            return $asesi;
        });
        $asesiForNotif = Asesi::with('student.user', 'sertification.skema', 'sertification.asesors.user')->findOrFail($asesi->id);
        $sertification = $asesiForNotif->sertification;
        $user = $asesiForNotif->student->user;

        $recipients = User::role('admin')->get();
        $asesors = $sertification->asesors;
        foreach ($asesors as $asesor) {
            if ($asesor->user) {
                $recipients->push($asesor->user);
            }
        }
        $recipients = $recipients->unique('id');

        if ($recipients->isNotEmpty()) {
            $title = 'Pendaftar Baru';
            $body = $user->name . ' telah mendaftar sertifikasi ' . $sertification->skema->nama_skema;
            $url = route('admin.sertifikasi.pendaftar.show', [$sertification, $asesiForNotif]);
            foreach ($recipients as $recipient) {
                $this->sendPushNotification($messaging, $recipient, $title, $body, $url, 'PendaftarBaru');
            }
        }
        return redirect(route('asesi.sertifikasi.applied.show', [$asesi->sertification_id, $asesi]))->with('message', 'Berhasil daftar sertifikasi');
    }

    public function showApplied(Sertification $sertification, Asesi $asesi, Request $request)
    {
        NotificationController::markAsRead($request);
        $asesi->load([
            'student.user',
            'asesifiles',
            'makulnilais',
            'sertifikat'
        ]);
        $student = $asesi->student;
        // dd($student);
        if ($asesi->student->user_id !== $request->user()->id) {
            abort(403);
        }
        return Inertia::render('Asesi/DetailSertifAsesi', [
            'sertification' => $sertification->load('skema'),
            'asesi' => $asesi,
            'student' => $student,
            'statusAksesMenuAsesmenOptions' => StatusAksesMenuAsesmen::options(),
            'statusBerkasAdministrasiOptions' => StatusBerkasAdministrasi::options(),
            'StatusFinalAsesiOptions' => StatusFinalAsesi::options(),
        ]);
    }

    public function updateApplied(Sertification $sertification, Asesi $asesi, Request $request, Messaging $messaging)
    {
        // dd($request);
        $asesi->load('student.user', 'asesifiles');
        $student = $asesi->student;
        $user = $student->user;
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
            'makulNilais' => 'required|array|min:1',
            'makulNilais.*.nama_makul' => 'required|string|max:255',
            'makulNilais.*.nilai_makul' => 'required|string|max:10',
            'apl_1' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('apl_1', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
                'max:2048'
            ],
            'apl_2' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_asesi', [])) && in_array('apl_2', $request->input('delete_files_asesi', []));
                }),
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
                'max:2048'
            ],
            'foto_ktp' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return is_array($request->input('delete_files_student', [])) && in_array('foto_ktp', $request->input('delete_files_student', []));
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
                    return is_array($request->input('delete_files_student', [])) && in_array('pas_foto', $request->input('delete_files_student', []));
                }),
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],

            'kartu_hasil_studi' => [
                function ($attribute, $value, $fail) use ($request, $asesi) {
                    $existingFilesCount = $asesi->asesifiles()->where('type', 'kartu_hasil_studi')->count();
                    $deleteFilesCount = 0;
                    if ($request->filled('delete_files_collection')) {
                        $deleteFilesCount = $asesi->asesifiles()
                            ->where('type', 'kartu_hasil_studi')->whereIn('id', $request->delete_files_collection)->count();
                    }
                    if (empty($value) && $existingFilesCount > 0 && $existingFilesCount === $deleteFilesCount) {
                        $fail('Field kartu hasil studi wajib diisi.');
                    }
                },
                'nullable',
                'array',
                'max:5'
            ],
            'kartu_hasil_studi.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'surat_ket_magang' => 'nullable|array|max:5',
            'surat_ket_magang.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'sertif_pelatihan' => 'nullable|array|max:5',
            'sertif_pelatihan.*' => 'file|mimes:jpg,jpeg,png,pdf|max:3072',
            'dok_pendukung_lain' => 'nullable|array|max:5',
            'dok_pendukung_lain.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'delete_files_collection' => 'nullable|array',
            'delete_files_collection.*' => 'integer|exists:asesifiles,id',
            'delete_files_student' => 'nullable|array',
            'delete_files_asesi' => 'nullable|array',
        ]);
        $shoulSendNotif = false;
        DB::transaction(function () use ($request, $student, $asesi, $user, &$shoulSendNotif) {
            $initialStatus = $asesi->status;
            $student->fill($request->only(['nik', 'tmpt_lhr', 'tgl_lhr', 'kelamin', 'kebangsaan', 'no_tlp_rmh', 'no_tlp_kntr', 'no_tlp_hp', 'kualifikasi_pendidikan',]));
            $user->fill($request->only(['no_tlp_hp', 'name']));
            $asesi->fill($request->only(['tujuan_sert',]));
            FileHelper::handleSingleFileDeletes($student, $request->input('delete_files_student', []));
            FileHelper::handleSingleFileDeletes($asesi, $request->input('delete_files_asesi', []));
            FileHelper::handleCollectionFileDeletes(Asesifile::class, $request->input('delete_files_collection', []));

            FileHelper::handleSingleFileUploads($student, ['foto_ktp', 'pas_foto'], $request, 'student_files');
            FileHelper::handleSingleFileUploads($asesi, ['apl_1', 'apl_2', 'foto_ktm'], $request, 'asesi_files');
            FileHelper::handleCollectionFileUploads(Asesifile::class,'asesi_id',$asesi->id, $request,['surat_ket_magang', 'sertif_pelatihan', 'dok_pendukung_lain', 'kartu_hasil_studi'], 'asesi_files');

            FileHelper::saveIfDirty([$student, $user, $asesi]);

            MakulNilai::where('asesi_id', $asesi->id)->delete();
            foreach ($request->makulNilais as $makul) {
                MakulNilai::create(['asesi_id' => $asesi->id,'nama_makul' => $makul['nama_makul'],'nilai_makul' => $makul['nilai_makul'],]);
            }

            if ($initialStatus === 'perlu_perbaikan_berkas') {
                $asesi->status = 'daftar';
                $asesi->catatan_perbaikan = null;
                $asesi->save();
                $shoulSendNotif = true;
            }
        });

        if ($shoulSendNotif) {
            $asesi->load('student.user', 'sertification.skema', 'sertification.asesors.user');
            $sertification = $asesi->sertification;
            $user = $asesi->student->user;

            $recipients = User::role('admin')->get();
            foreach ($sertification->asesors as $asesor) {
                if ($asesor->user) {
                    $recipients->push($asesor->user);
                }
            }
            $recipients = $recipients->unique('id');

            if ($recipients->isNotEmpty()) {
                $title = 'Berkas Diperbaiki';
                $body = $user->name . ' telah memperbaiki dan mengirim ulang berkas untuk sertifikasi ' . $sertification->skema->nama_skema;
                $url = route('admin.sertifikasi.pendaftar.show', [$sertification, $asesi]);

                foreach ($recipients as $recipient) {
                    $this->sendPushNotification($messaging, $recipient, $title, $body, $url, 'BerkasDiperbaiki');
                }
            }
        }
        return redirect()->back()->with('message', 'Berhasil update data sertifikasi');
    }

    public function batal_mendaftar($sert_id, $asesi_id)
    {
        $asesi = Asesi::with(['makulnilais', 'asesifiles', 'transaction'])->findOrFail($asesi_id);

        $student = $asesi->student;
        if ($student->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan melakukan aksi ini.');
        }
        DB::transaction(function () use ($asesi) {

            foreach (['apl_1', 'apl_2', 'foto_ktm'] as $fileField) {
                if ($asesi->$fileField && Storage::disk('public')->exists($asesi->$fileField)) {
                    Storage::disk('public')->delete($asesi->$fileField);
                }
            }
            foreach ($asesi->asesifiles as $file) {
                if (Storage::disk('public')->exists($file->path_file)) {
                    Storage::disk('public')->delete($file->path_file);
                }
            }
            $asesi->makulnilais()->delete();
            $asesi->asesifiles()->delete();
            if ($asesi->transaction) {
                $asesi->transaction()->delete();
            }

            $asesi->delete();
        });

        return redirect(route('asesi.sertifikasi.list'))->with('message', 'Pendaftaran sertifikasi berhasil dibatalkan.');
    }

    
}
