<?php

use App\Http\Controllers\Admin\Sertifikasi\AsesmenController;
use App\Http\Controllers\Admin\Sertifikasi\KelolaSertifikasiController;
use App\Http\Controllers\Asesi\Sertifikasi\PembayaranAsesiController;
use App\Http\Controllers\Admin\Sertifikasi\PembayaranController;
use App\Http\Controllers\Admin\Sertifikasi\PendaftarController;
use App\Http\Controllers\Admin\Sertifikasi\PengumumanController;
use App\Http\Controllers\ApplyCertificationController;
use App\Http\Controllers\Asesi\Sertifikasi\AsesmenAsesiController;
use App\Http\Controllers\Asesi\Sertifikasi\PengumumanAsesiController;
use App\Http\Controllers\ManageCertificationController;
use App\Http\Controllers\AsesiController;
use App\Http\Controllers\AsesorController;
use App\Http\Controllers\ManageSkemaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SertificationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Models\Sertification;
use App\Models\Asesi;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Notifications\PendaftarBaru;

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');
Route::get('/test', function () {
    return view('dumpbladefiles.testing-file',[
        'sertification' => Sertification::find(1)
    ]);
});


// punya semua user yg terautentikasi
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile_asesi', [ProfileController::class, 'edit_asesi'])->name('profile_asesi.edit');
    Route::patch('/profile_asesi', [ProfileController::class, 'update_asesi'])->name('profile_asesi.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); 
});



//versi lebih baik asesor admin
//nanti harus dikasih middleware 'verified'
Route::middleware(['auth', 'role:admin|asesor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboardadmin.dashboardadmin');
    })->name('dashboard'); // Hasil: admin.dashboard

    // Menggunakan Route::resource untuk CRUD standar, lebih bersih.
    Route::prefix('skema')->name('skema.')->group(function () {
        Route::get('/', [ManageSkemaController::class, 'create'])->name('create'); // admin.skema.create
        Route::post('/', [ManageSkemaController::class, 'store'])->name('store'); // admin.skema.store
        Route::get('/{id}/edit', [ManageSkemaController::class, 'edit'])->name('edit'); // admin.skema.edit
        Route::patch('/{id}/update', [ManageSkemaController::class, 'update'])->name('update'); // admin.skema.update
        Route::delete('/{id}/destroy', [ManageSkemaController::class, 'destroy'])->name('destroy'); // admin.skema.destroy
    });
    Route::resource('asesor', AsesorController::class)->names('asesor'); // URI: admin/asesor
    Route::prefix('kelolasertifikasi')->name('kelolasertifikasi.')->group(function () {
        Route::get('/index', [KelolaSertifikasiController::class, 'index'])->name('index'); // admin.kelolasertifikasi.index
        Route::post('/store', [KelolaSertifikasiController::class, 'store'])->name('store'); // admin.kelolasertifikasi.store
        Route::get('/{sert_id}/show', [KelolaSertifikasiController::class, 'show'])->name('show'); // admin.kelolasertifikasi.show
        Route::get('/{sert_id}/edit', [KelolaSertifikasiController::class, 'edit'])->name('edit'); // admin.kelolasertifikasi.edit
        Route::patch('/{sert_id}/update', [KelolaSertifikasiController::class, 'update'])->name('update'); // admin.kelolasertifikasi.update
        Route::delete('/{sert_id}/destroy', [KelolaSertifikasiController::class, 'destroy'])->name('destroy'); // admin.kelolasertifikasi.destroy
        // Route::patch('/{sert_id}/update_status', [KelolaSertifikasiController::class, 'complete'])->name('update_status'); // admin.kelolasertifikasi.update_status
    });
    // about to go down
    Route::resource('sertification', SertificationController::class)->names('sertification'); // URI: admin/sertification

    // Route::post('/sertification/{sertification}/complete', [SertificationController::class, 'complete'])->name('sertification.complete');
    // Mengelola konten (pra-asesmen & asesmen) untuk sebuah sertifikasi
    Route::prefix('sertification/{id}')->name('sertification.')->group(function () {
        // Menampilkan daftar asesi/pendaftar untuk sertifikasi ini
        Route::get('/applicants', [ManageCertificationController::class, 'list_asesi'])->name('applicants.index'); // admin.sertification.applicants.index
        // Mengelola pembayaran asesi
        Route::get('/payment-desc/index', [PembayaranController::class, 'index_rincian_pembayaran'])->name('payment-desc.index'); // admin.sertification.payment-desc.index
        Route::patch('/payment-desc/update', [PembayaranController::class, 'update_rincian_pembayaran'])->name('payment-desc.update'); // admin.sertification.payment-desc.update

        // Mengelola konten pra-asesmen
        // Route::get('/pre-assessment/edit', [ManageCertificationController::class, 'rincian_praasesmen'])->name('pre-assessment.edit'); // admin.sertification.pre-assessment.edit
        // Route::patch('/pre-assessment/update', [ManageCertificationController::class, 'update_rincian_praasesmen'])->name('pre-assessment.update'); // admin.sertification.pre-assessment.update

        // Mengelola konten asesmen
        Route::get('/assessment-announcement/index', [PengumumanController::class, 'index_pengumuman_asesmen'])->name('assessment-announcement.index'); // admin.sertification.assessment-announcement.index
        Route::post('/assessment-announcement/store', [PengumumanController::class, 'store_pengumuman_asesmen'])->name('assessment-announcement.store'); // admin.sertification.assessment-announcement.store
        Route::get('/assessment-announcement/edit/{peng_id}', [PengumumanController::class, 'edit_pengumuman_asesmen'])->name('assessment-announcement.edit'); // admin.sertification.assessment-announcement.edit
        Route::patch('/assessment-announcement/update/{peng_id}', [PengumumanController::class, 'update_pengumuman_asesmen'])->name('assessment-announcement.update'); // admin.sertification.assessment-announcement.update
        Route::delete('/assessment-announcement/destroy/{peng_id}', [PengumumanController::class, 'destroy_pengumuman_asesmen'])->name('assessment-announcement.destroy'); // admin.sertification.assessment-announcement.update
        Route::get('/assessment/edit', [AsesmenController::class, 'rincian_asesmen'])->name('assessment.edit'); // admin.sertification.assessment.edit
        Route::patch('/assessment/update', [AsesmenController::class, 'update_tugas_asesmen'])->name('assessment.update'); // admin.sertification.assessment.update
        Route::get('/report', [ManageCertificationController::class, 'rincian_laporan'])->name('report'); // admin.sertification.report
        Route::post('/filter-riwayat_sertifikasi', [ManageCertificationController::class, 'filter_riwayat_sertifikasi'])->name('.filter-riwayat_sertifikasi'); // admin.sertification.filter-riwayat_sertifikasi
    });

    // Mengelola data pendaftar (asesi) secara spesifik
    Route::prefix('applicants/{id}')->name('applicants.')->group(function () {
        Route::get('/', [ManageCertificationController::class, 'rincian_data_asesi'])->name('show'); // admin.applicants.show
        Route::patch('/{sert_id}/update-status', [ManageCertificationController::class, 'updateStatus'])->name('update-status'); // admin.applicants.update-status
        Route::patch('/{transaction_id}/update-payment-status', [PendaftarController::class, 'update_status_pembayaran'])->name('update-payment-status'); // admin.applicants.update-payment-status
        Route::get('/{sert_id}/assessment-asesi/show', [AsesmenController::class, 'rincian_asesmen_asesi'])->name('assessment-asesi.show'); // admin.applicants.assessment-asesi.show
        Route::patch('/{sert_id}/upload-certificate/patch', [PendaftarController::class, 'upload_certificate'])->name('upload-certificate.update'); // admin.applicants.upload-certificate.update
    });

    // Route untuk AJAX file delete (lebih baik dibuat spesifik)
    // Contoh: Route::delete('/pre-assessment-files/{file}', ...)->name('pre-assessment-files.destroy');
    // Route::delete('/praasesmen-file-ajax-delete', [ManageCertificationController::class, 'ajaxDeletePraasesmenFile'])->name('praasesmen.file.ajaxdelete');
    Route::delete('/asesmen-file-ajax-delete', [ManageCertificationController::class, 'ajaxDeleteAsesmenFile'])->name('asesmen.file.ajaxdelete');
    Route::delete('/pengumuman-file-ajax-delete', [PengumumanController::class, 'ajaxDeletePengumumanAsesmenFile'])->name('pengumuman.file.ajaxdelete');
});

//versi lebih baik utk asesi, konsistensi bahasa, penggunaan kebab-case, pakai konvensi restful (index, create, store, show, edit, update, destroy), kelompokin route dgn prefix
Route::middleware(['auth', 'role:asesi'])->prefix('asesi')->name('asesi.')->group(function () {
    Route::get('/dashboard', function () {
        return view('asesi.dashboardasesi');
    })->name('dashboard'); // Hasil: asesi.dashboard

    // Daftar sertifikasi yang tersedia untuk diajukan
    Route::get('/certifications', [ManageCertificationController::class, 'asesi_index_sertifikasi'])->name('certifications.index'); //asesi.certifications.index

    // Proses pengajuan sertifikasi (apply)
    // Asumsi {sertification} adalah ID dari skema sertifikasi yang akan diajukan
    Route::get('/certifications/{sert_id}/apply', [ManageCertificationController::class, 'apply_sertifikasi'])->name('certifications.apply.create'); //asesi.certifications.apply.create
    Route::post('/certifications/apply', [ApplyCertificationController::class, 'store'])->name('certifications.apply.store'); //asesi.certifications.apply.store

    // Mengelola sertifikasi yang sudah diajukan (applied)
    // Asumsi {asesi} adalah model Asesi yang merupakan "aplikasi" dari user tersebut
    Route::prefix('applied-certifications/{sert_id}')->name('applied.')->group(function () {
        Route::get('{asesi_id}/show', [ManageCertificationController::class, 'show_applied_sertifikasi'])->name('show'); // asesi.applied.show
        Route::get('{asesi_id}/edit', [ManageCertificationController::class, 'edit_apply_sertifikasi'])->name('edit'); // asesi.applied.edit
        Route::patch('{asesi_id}/update', [ApplyCertificationController::class, 'update'])->name('update'); // asesi.applied.update

        // Pra-asesmen, Asesmen, dan Pembayaran untuk aplikasi spesifik
        // Route::get('/pre-assessment', [ManageCertificationController::class, 'rincian_praasesmen_asesi'])->name('pre-assessment.show'); // asesi.applied.pre-assessment.show
        Route::get('/{asesi_id}/assessment-announcement/index', [PengumumanAsesiController::class, 'index_pengumuman_asesi'])->name('assessment-announcement.index'); // asesi.applied.assessment-announcement.index
        Route::get('/{asesi_id}/assessment', [AsesmenAsesiController::class, 'index_asesmen_asesi'])->name('assessment.index'); // asesi.applied.assessment.show
        Route::post('/{asesi_id}/assessment', [AsesmenAsesiController::class, 'update_asesmen_asesi'])->name('assessment.update'); // asesi.applied.assessment.update

        // Payment Routes
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/{asesi_id}', [PembayaranAsesiController::class, 'index_rincian_pembayaran'])->name('create'); // asesi.applied.payment.create
            Route::post('/{asesi_id}', [PembayaranAsesiController::class, 'upload_bukti_pembayaran'])->name('store'); // asesi.applied.payment.store
            Route::post('{asesi_id}/checkout', [PaymentController::class, 'checkout'])->name('checkout'); // asesi.applied.payment.checkout
            // Route::post('{asesi_id}/manual', [PaymentController::class, 'buktipembayaran'])->name('manual.store'); // asesi.applied.payment.manual.store
        });
    });
});

// Route untuk menerima notifikasi (webhook) dari Midtrans.
// Route ini harus di luar middleware 'auth' karena diakses oleh server Midtrans.
Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook'])->name('midtrans.webhook');
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
require __DIR__ . '/auth.php';
