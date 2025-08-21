<?php

use App\Http\Controllers\Admin\Sertifikasi\AsesmenController;
use App\Http\Controllers\Admin\Sertifikasi\KelolaSertifikasiController;
use App\Http\Controllers\Admin\Sertifikasi\PembayaranController;
use App\Http\Controllers\Admin\Sertifikasi\PendaftarController;
use App\Http\Controllers\Admin\Sertifikasi\PengumumanController;
use App\Http\Controllers\Asesi\Sertifikasi\PembayaranAsesiController;
use App\Http\Controllers\Asesi\Sertifikasi\AsesmenAsesiController;
use App\Http\Controllers\Asesi\Sertifikasi\PengumumanAsesiController;
use App\Http\Controllers\Asesi\Sertifikasi\KelolaSertifikasiAsesiController;
use App\Http\Controllers\AsesorController;
use App\Http\Controllers\ManageSkemaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Models\Sertification;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

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
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
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
        Route::post('/filter-riwayat_sertifikasi', [KelolaSertifikasiController::class, 'filter_riwayat_sertifikasi'])->name('.filter-riwayat_sertifikasi'); // admin.kelolasertifikasi.filter-riwayat_sertifikasi
        Route::get('/index', [KelolaSertifikasiController::class, 'index'])->name('index'); // admin.kelolasertifikasi.index
        Route::post('/store', [KelolaSertifikasiController::class, 'store'])->name('store'); // admin.kelolasertifikasi.store
        Route::get('/{sert_id}/show', [KelolaSertifikasiController::class, 'show'])->name('show'); // admin.kelolasertifikasi.show
        Route::get('/{sert_id}/edit', [KelolaSertifikasiController::class, 'edit'])->name('edit'); // admin.kelolasertifikasi.edit
        Route::patch('/{sert_id}/update', [KelolaSertifikasiController::class, 'update'])->name('update'); // admin.kelolasertifikasi.update
        Route::delete('/{sert_id}/destroy', [KelolaSertifikasiController::class, 'destroy'])->name('destroy'); // admin.kelolasertifikasi.destroy
        Route::get('/{sert_id}/report', [KelolaSertifikasiController::class, 'rincian_laporan'])->name('report'); // admin.kelolasertifikasi.report
    });
    Route::prefix('sertifikasi/{sert_id}')->name('sertifikasi.')->group(function () {
        // untuk munculin halaman edit asesmen dan updatenya
        Route::get('/assessment/edit', [AsesmenController::class, 'rincian_asesmen'])->name('assessment.edit'); // admin.sertification.assessment.edit
        Route::patch('/assessment/update', [AsesmenController::class, 'update_tugas_asesmen'])->name('assessment.update'); // admin.sertification.assessment.update
        // untuk mnunculin halaman edit rincian pembayaran dan updatenya
        Route::get('/payment-desc/index', [PembayaranController::class, 'index_rincian_pembayaran'])->name('payment-desc.index'); // admin.sertification.payment-desc.index
        Route::patch('/payment-desc/update', [PembayaranController::class, 'update_rincian_pembayaran'])->name('payment-desc.update'); // admin.sertification.payment-desc.update
        // Mengelola konten pengumuman
        Route::get('/assessment-announcement/index', [PengumumanController::class, 'index_pengumuman_asesmen'])->name('assessment-announcement.index'); // admin.sertification.assessment-announcement.index
        Route::post('/assessment-announcement/store', [PengumumanController::class, 'store_pengumuman_asesmen'])->name('assessment-announcement.store'); // admin.sertification.assessment-announcement.store
        Route::get('/assessment-announcement/edit/{peng_id}', [PengumumanController::class, 'edit_pengumuman_asesmen'])->name('assessment-announcement.edit'); // admin.sertification.assessment-announcement.edit
        Route::patch('/assessment-announcement/update/{peng_id}', [PengumumanController::class, 'update_pengumuman_asesmen'])->name('assessment-announcement.update'); // admin.sertification.assessment-announcement.update
        Route::delete('/assessment-announcement/destroy/{peng_id}', [PengumumanController::class, 'destroy_pengumuman_asesmen'])->name('assessment-announcement.destroy'); // admin.sertification.assessment-announcement.update
    });

    // Mengelola data pendaftar (asesi) secara spesifik
    Route::prefix('sertifikasi/{sert_id}')->name('sertifikasi.')->group(function () {
        Route::get('/pendaftar/index', [PendaftarController::class, 'list_asesi'])->name('pendaftar.index'); // admin.sertifikasi.pendaftar.index
        Route::get('/{asesi_id}/pendaftar/show', [PendaftarController::class, 'rincian_data_asesi'])->name('pendaftar.show'); // admin.sertifikasi.pendaftar.show
        Route::patch('/{asesi_id}/update-status', [PendaftarController::class, 'update_status_asesi'])->name('pendaftar.update-status'); // admin.applicants.update-status
        Route::patch('/{transaction_id}/update-payment-status', [PendaftarController::class, 'update_status_pembayaran'])->name('pendaftar.update-payment-status'); // admin.applicants.update-payment-status
        Route::patch('/{asesi_id}/upload-certificate/patch', [PendaftarController::class, 'upload_certificate'])->name('pendaftar.upload-certificate.update'); // admin.applicants.upload-certificate.update
    });

    Route::delete('/asesmen-file-ajax-delete', [AsesmenController::class, 'ajaxDeleteAsesmenFile'])->name('asesmen.file.ajaxdelete');
    Route::delete('/pengumuman-file-ajax-delete', [PengumumanController::class, 'ajaxDeletePengumumanAsesmenFile'])->name('pengumuman.file.ajaxdelete');
});

//versi lebih baik utk asesi, konsistensi bahasa, penggunaan kebab-case, pakai konvensi restful (index, create, store, show, edit, update, destroy), kelompokin route dgn prefix
Route::middleware(['auth', 'role:asesi'])->prefix('asesi')->name('asesi.')->group(function () {
    Route::get('/dashboard', function () {
        return view('asesi.dashboardasesi.dashboardasesi');
    })->name('dashboard'); // Hasil: asesi.dashboard

    Route::get('/{sert_id}/{asesi_id}/assessmen/index', [AsesmenAsesiController::class, 'index_asesmen_asesi'])->name('assessmen.index'); // asesi.assessmen.index
    Route::post('/{sert_id}/{asesi_id}/assessmen', [AsesmenAsesiController::class, 'update_asesmen_asesi'])->name('assessmen.update'); // asesi.assessmen.update
    Route::prefix('payment/{sert_id}')->name('payment.')->group(function () {
        Route::get('/{asesi_id}/create', [PembayaranAsesiController::class, 'index_rincian_pembayaran'])->name('create'); // asesi.payment.create
        Route::post('/{asesi_id}/store', [PembayaranAsesiController::class, 'upload_bukti_pembayaran'])->name('store'); // asesi.payment.store
        // Route::post('{asesi_id}/checkout', [PaymentController::class, 'checkout'])->name('checkout'); // asesi.payment.checkout
        // Route::post('{asesi_id}/manual', [PaymentController::class, 'buktipembayaran'])->name('manual.store'); // asesi.payment.manual.store
    });
    Route::prefix('sertifikasi')->name('sertifikasi.')->group(function () {
        Route::get('/index', [KelolaSertifikasiAsesiController::class, 'asesi_daftar_sertifikasi'])->name('index'); // asesi.sertifikasi.index
        Route::get('/{sert_id}/apply/create', [KelolaSertifikasiAsesiController::class, 'form_daftar_sertifikasi'])->name('apply.create'); // asesi.sertifikasi.apply.create
        Route::post('/apply/store', [KelolaSertifikasiAsesiController::class, 'submit_form_daftar_sertifikasi'])->name('applied.store'); // asesi.sertifikasi.apply.store
        Route::get('/{sert_id}/{asesi_id}/applied/show', [KelolaSertifikasiAsesiController::class, 'detail_applied_sertifikasi'])->name('applied.show'); // asesi.sertifikasi.applied.show
        Route::get('/{sert_id}/{asesi_id}/applied/edit', [KelolaSertifikasiAsesiController::class, 'edit_applied_sertifikasi'])->name('applied.edit'); // asesi.sertifikasi.applied.edit
        Route::patch('/{sert_id}/{asesi_id}/applied/update', [KelolaSertifikasiAsesiController::class, 'update_applied_sertifikasi'])->name('applied.update'); // asesi.sertifikasi.applied.update
    });
    Route::get('/{sert_id}/{asesi_id}/pengumuman/index', [PengumumanAsesiController::class, 'index_pengumuman_asesi'])->name('pengumuman.index'); // asesi.pengumuman.index
    
});

// Route untuk menerima notifikasi (webhook) dari Midtrans.
// Route ini harus di luar middleware 'auth' karena diakses oleh server Midtrans.
Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook'])->name('midtrans.webhook');
require __DIR__ . '/auth.php';
