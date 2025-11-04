<?php

namespace App\Http\Controllers\Asesi\Sertifikasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Asesi;
use App\Models\Student;
use App\Models\User;
use App\Models\Asesor;
use App\Models\NotificationLog;
use App\Models\Makulnilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sertification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use App\Models\Asesifile;
use App\Notifications\PendaftarBaru;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Notification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification; // <-- IMPORT INI
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class KelolaSertifikasiAsesiController extends Controller
{

    public function asesi_daftar_sertifikasi(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $asesi = Asesi::where('student_id', $student->id)
            ->get()
            ->keyBy('sertification_id');
        $sertifications = Sertification::with('skema','paymentInstruction')
            ->where('status', 'berlangsung')
            ->orderBy('tgl_apply_dibuka', 'desc')
            ->get();
        return Inertia::render('Asesi/SertifikasiList', [
            'sertifications' => $sertifications,
            'asesi' => $asesi
        ]);
    }

    public function form_daftar_sertifikasi($sert_id, Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        // dd($student);
        $existingAsesi = Asesi::where('student_id', $student->id)->where('sertification_id', $sert_id)->first();
        if ($existingAsesi) {
            return redirect()->route('asesi.sertifikasi.applied.show', [$sert_id, $existingAsesi->id])->with('message', 'Anda sudah terdaftar pada skema sertifikasi ini.');
        }
        return Inertia::render('Asesi/ApplySertifAsesi', [
            'sertification' => Sertification::with('skema')->findOrFail($sert_id),
            'student' => $student,
            'user' => $user,
        ]);
    }

    public function submit_form_daftar_sertifikasi($student_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $student = Student::with('user')->findOrFail($student_id);
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
        $asesi = DB::transaction(function () use ($request, $student, $messaging) {

            $user = $student->user;
            $student->fill($request->only([
                'nik',
                'tmpt_lhr',
                'tgl_lhr', 
                'kelamin',
                'kebangsaan',
                'no_tlp_rmh',
                'no_tlp_kntr',
                'kualifikasi_pendidikan',
            ]));
            $user->fill($request->only(['no_tlp_hp', 'name']));
            if ($request->filled('delete_files')) {
                foreach ($request->delete_files as $fieldName) {
                    if ($student->$fieldName && Storage::disk('public')->exists($student->$fieldName)) {
                        Storage::disk('public')->delete($student->$fieldName);
                        $student->$fieldName = null;
                    }
                }
            }
            foreach (['foto_ktp', 'pas_foto'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $student->$fileField = FileHelper::storeFileWithUniqueName($request->file($fileField), 'student_files')['path'];
                }
            }
            if ($student->isDirty()) {
                $student->save();
            }
            if ($user->isDirty()) {
                $user->save();
            }
            $asesiData = [
                'student_id' => $student->id,
                'sertification_id' => $request->input('sertification_id'),
                'tujuan_sert' => $request->input('tujuan_sert'),
            ];
            foreach (['apl_1', 'apl_2', 'foto_ktm'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $asesiData[$fileField] = FileHelper::storeFileWithUniqueName($request->file($fileField), 'asesi_files')['path'];
                }
            }
            $asesi = Asesi::create($asesiData);
            $sertification = Sertification::with('asesors.user')->findOrFail($asesi->sertification_id);
            foreach ($request->makulNilais as $makul) {
                MakulNilai::create([
                    'asesi_id' => $asesi->id,
                    'nama_makul' => $makul['nama_makul'],
                    'nilai_makul' => $makul['nilai_makul'],
                ]);
            }
            // buat input multiple 
            $fileTypes = ['surat_ket_magang', 'sertif_pelatihan', 'dok_pendukung_lain', 'kartu_hasil_studi'];

            foreach ($fileTypes as $fileType) {
                if ($request->hasFile($fileType)) {
                    foreach ($request->file($fileType) as $file) {
                        $fileData = FileHelper::storeFileWithUniqueName($file, "asesi_files")['path'];
                        Asesifile::create([
                            'asesi_id' => $asesi->id,
                            'type' => $fileType,
                            'path_file' => $fileData,
                        ]);
                    }
                }
            }

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
                $url = route('admin.sertifikasi.pendaftar.show', [$sertification->id, $asesi->id]);
                foreach ($recipients as $recipient) {
                    NotificationLog::create([
                        'user_id' => $recipient->id,
                        'type' => 'PendaftarBaru',
                        'message' => $body,
                        'link' => $url,
                    ]);
                }
                $pushRecipients = $recipients->whereNotNull('fcm_token');
                if ($pushRecipients->isNotEmpty()) {
                    $tokens = $pushRecipients->pluck('fcm_token')->toArray();
                    $message = CloudMessage::new()
                        ->withNotification(FirebaseNotification::create($title, $body))
                        ->withData(['url' => $url]);
                    try {
                        $report = $messaging->sendMulticast($message, $tokens);
                        if ($report->hasFailures()) {
                            $invalidTokens = $report->invalidTokens();
                            if (!empty($invalidTokens)) {
                                Log::warning('Menghapus token FCM yang tidak valid/kedaluwarsa.', ['tokens' => $invalidTokens]);
                                User::whereIn('fcm_token', $invalidTokens)->update(['fcm_token' => null]);
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::error("Gagal mengirim multicast push notification: " . $e->getMessage());
                    }
                }
            }

            return $asesi;
        });
        return redirect(route('asesi.sertifikasi.applied.show', [$asesi->sertification_id, $asesi->id]))->with('message', 'Berhasil daftar sertifikasi');
    }

    public function detail_applied_sertifikasi($sert_id, $asesi_id, Request $request)
    {
        NotificationController::markAsRead($request);
        $asesi = Asesi::with([
            'student.user',
            'asesifiles',
            'makulnilais',
            'transaction' => fn($q) => $q->latest(),
            'sertifikat'
        ])->findOrFail($asesi_id);
        $asesi->latest_transaction = $asesi->transaction->first();
        $student = $asesi->student;
        // dd($student);
        // Pastikan asesi ini milik user yang sedang login
        if ($asesi->student->user_id !== $request->user()->id) {
            abort(403);
        }
        return Inertia::render('Asesi/DetailSertifAsesi', [
            'sertification' => Sertification::with('skema')->findOrFail($sert_id),
            'asesi' => $asesi,
            'student' => $student,
        ]);
    }

    public function update_applied_sertifikasi($sert_id, $asesi_id, Request $request, Messaging $messaging)
    {
        // dd($request);
        $asesi = Asesi::with('student.user', 'asesifiles')->findOrFail($asesi_id);
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

        DB::transaction(function () use ($request, $student, $asesi, $user, $messaging) {
            $initialStatus = $asesi->status;
            $student->fill($request->only([
                'nik',
                'tmpt_lhr',
                'tgl_lhr',
                'kelamin',
                'kebangsaan',
                'no_tlp_rmh',
                'no_tlp_kntr',
                'no_tlp_hp',
                'kualifikasi_pendidikan',
            ]));
            if ($request->filled('delete_files_student')) {
                foreach ($request->delete_files_student as $fieldName) {
                    if ($student->$fieldName && Storage::disk('public')->exists($student->$fieldName)) {
                        Storage::disk('public')->delete($student->$fieldName);
                        $student->$fieldName = null;
                    }
                }
            }
            if ($request->filled('delete_files_asesi')) {
                foreach ($request->delete_files_asesi as $fieldName) {
                    if ($asesi->$fieldName && Storage::disk('public')->exists($asesi->$fieldName)) {
                        Storage::disk('public')->delete($asesi->$fieldName);
                        $asesi->$fieldName = null;
                    }
                }
            }
            if ($request->filled('delete_files_collection')) {
                $filesToDelete = Asesifile::whereIn('id', $request->delete_files_collection)->get();
                foreach ($filesToDelete as $file) {
                    if (Storage::disk('public')->exists($file->path_file)){
                        Storage::disk('public')->delete($file->path_file);
                        $file->delete();
                    }
                }
            }
            $user->fill($request->only(['no_tlp_hp', 'name']));
            foreach (['foto_ktp', 'pas_foto'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $student->$fileField = FileHelper::storeFileWithUniqueName($request->file($fileField), 'student_files')['path'];
                }
            }
            if ($student->isDirty()) {
                $student->save();
            }
            if ($user->isDirty()) {
                $user->save();
            }
            // untuk tabel asesi
            $asesi->fill($request->only([
                'tujuan_sert',
            ]));
            foreach (['apl_1', 'apl_2', 'foto_ktm'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $asesi->$fileField = FileHelper::storeFileWithUniqueName($request->file($fileField), 'asesi_files')['path'];
                }
            }
            if ($asesi->isDirty()) {
                $asesi->save();
            }
            
            MakulNilai::where('asesi_id', $asesi->id)->delete();
            foreach ($request->makulNilais as $makul) {
                MakulNilai::create([
                    'asesi_id' => $asesi->id,
                    'nama_makul' => $makul['nama_makul'],
                    'nilai_makul' => $makul['nilai_makul'],
                ]);
            }
            //untuk input multiple
            $fileTypes = ['surat_ket_magang', 'sertif_pelatihan', 'dok_pendukung_lain', 'kartu_hasil_studi'];
            foreach ($fileTypes as $fileType) {
                if ($request->hasFile($fileType)) {
                    foreach ($request->file($fileType) as $file) {
                        $fileData = FileHelper::storeFileWithUniqueName($file, "asesi_files")['path'];
                        Asesifile::create([
                            'asesi_id' => $asesi->id,
                            'type' => $fileType,
                            'path_file' => $fileData,
                        ]);
                    }
                }
            }
            if ($initialStatus === 'perlu_perbaikan_berkas') {
                $asesi->status = 'daftar';
                $asesi->catatan_perbaikan = null;
                $asesi->save();

                $sertification = Sertification::with('skema', 'asesors.user')->findOrFail($asesi->sertification_id);
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
                    $url = route('admin.sertifikasi.pendaftar.show', [$sertification->id, $asesi->id]);

                    foreach ($recipients as $recipient) {
                        NotificationLog::create([
                            'user_id' => $recipient->id,
                            'type' => 'BerkasDiperbaiki',
                            'message' => $body,
                            'link' => $url,
                        ]);
                    }

                    $pushRecipients = $recipients->whereNotNull('fcm_token');
                    if ($pushRecipients->isNotEmpty()) {
                        $tokens = $pushRecipients->pluck('fcm_token')->toArray();
                        $message = CloudMessage::new()
                            ->withNotification(FirebaseNotification::create($title, $body))
                            ->withData(['url' => $url]);
                        try {
                            $messaging->sendMulticast($message, $tokens);
                        } catch (\Throwable $e) {
                            Log::error("Gagal mengirim notifikasi perbaikan berkas: " . $e->getMessage());
                        }
                    }
                }
            }

        });
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
