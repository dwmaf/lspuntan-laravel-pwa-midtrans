<?php

use App\Http\Controllers\Admin\Sertifikasi\AsesmenController;
use App\Http\Controllers\Admin\Sertifikasi\KelolaSertifikasiController;
use App\Http\Controllers\Admin\Sertifikasi\PembayaranController;
use App\Http\Controllers\Admin\Sertifikasi\PendaftarController;
use App\Http\Controllers\Admin\Sertifikasi\PengumumanController;
use App\Http\Controllers\Admin\SkemaController;
use App\Http\Controllers\Asesi\Sertifikasi\PembayaranAsesiController;
use App\Http\Controllers\Asesi\Sertifikasi\AsesmenAsesiController;
// use App\Http\Controllers\Asesi\Sertifikasi\PengumumanAsesiController;
use App\Http\Controllers\Asesi\Sertifikasi\KelolaSertifikasiAsesiController;
use App\Http\Controllers\Admin\AsesorController;
use App\Http\Controllers\Asesi\Sertifikasi\PengumumanAsesiController;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfflineController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Public\CertificateVerificationController;
use App\Models\Sertification;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Vite;
use Inertia\Inertia;

Route::get('/debug-firebase', function () { //will be commented on production
    try {
        $messaging = app('firebase.messaging');
        dd('Koneksi ke Firebase Messaging BERHASIL! Driver seharusnya sudah terdaftar. Masalah ada di tempat lain.');
    } catch (\Throwable $e) {
        dd($e);
    }
});

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canResetPassword' => Route::has('password.request'),
        'status' => session('status'),
    ]);
})->middleware('guest');

Route::get('/test', function () { //will be commented on production
    return view('dumpbladefiles.testing-file', [
        'sertification' => Sertification::find(1)
    ]);
});
// Route::get('/offline', OfflineController::class)->name('laravelpwa.offline');
Route::get('/serviceworker.js', function () {
    if (config('app.debug') === false) {
        $jsUrl = Vite::asset('resources/js/app.js');
        $cssUrl = Vite::asset('resources/css/app.css');
        $assetUrls = [$jsUrl, $cssUrl];
    } else {
        $assetUrls = []; // Kosongkan saat development
    }

    $data = [
        'isDebug' => config('app.debug'), // Kirim status debug ke view
        'assetUrls' => $assetUrls,
        'offlineUrl' => route('laravelpwa.offline'),
    ];

    return response()
        ->view('serviceworker', $data) // Kirim data ke view
        ->header('Content-Type', 'application/javascript');
})->name('pwa.serviceworker');

Route::get('/verify-certificate', [CertificateVerificationController::class, 'index'])->name('certificate.verify');

// punya semua user yg terautentikasi
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile_asesi', [ProfileController::class, 'edit_asesi'])->name('profile_asesi.edit');
    Route::patch('/profile_asesi', [ProfileController::class, 'update_asesi'])->name('profile_asesi.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); 
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/fcm/token', [FcmController::class, 'saveToken'])->name('fcm.token');
    Route::get('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});



//versi lebih baik asesor admin
//nanti harus dikasih middleware 'verified'
Route::middleware(['auth', 'role:admin|asesor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/DashboardAdmin');
    })->name('dashboard'); //admin.dashboard

    Route::prefix('skema')->name('skema.')->group(function () {
        Route::get('/', [SkemaController::class, 'create'])->name('create'); // admin.skema.create
        Route::post('/', [SkemaController::class, 'store'])->name('store'); // admin.skema.store
        Route::patch('/{id}/update', [SkemaController::class, 'update'])->name('update'); // admin.skema.update
        Route::delete('/{id}/destroy', [SkemaController::class, 'destroy'])->name('destroy'); // admin.skema.destroy
    });
    Route::prefix('asesor')->name('asesor.')->group(function () {
        Route::get('/index', [AsesorController::class, 'index'])->name('index'); // admin.asesor.index
        Route::post('/', [AsesorController::class, 'store'])->name('store'); // admin.asesor.store
        Route::patch('/{id}/update', [AsesorController::class, 'update'])->name('update'); // admin.asesor.update
        Route::delete('/{id}/destroy', [AsesorController::class, 'destroy'])->name('destroy'); // admin.asesor.destroy
    });
    Route::prefix('kelolasertifikasi')->name('kelolasertifikasi.')->group(function () {
        // Route::post('/filter-riwayat_sertifikasi', [KelolaSertifikasiController::class, 'filter_riwayat_sertifikasi'])->name('.filter-riwayat_sertifikasi'); // admin.kelolasertifikasi.filter-riwayat_sertifikasi
        Route::get('/index', [KelolaSertifikasiController::class, 'index'])->name('index'); // admin.kelolasertifikasi.index
        Route::post('/store', [KelolaSertifikasiController::class, 'store'])->name('store'); // admin.kelolasertifikasi.store
        Route::get('/{sert_id}/show', [KelolaSertifikasiController::class, 'show'])->name('show'); // admin.kelolasertifikasi.show
        Route::patch('/{sert_id}/update', [KelolaSertifikasiController::class, 'update'])->name('update'); // admin.kelolasertifikasi.update
        Route::delete('/{sert_id}', [KelolaSertifikasiController::class, 'destroy'])->name('destroy'); // admin.kelolasertifikasi.destroy
        Route::get('/{sert_id}/report', [KelolaSertifikasiController::class, 'rincian_laporan'])->name('report'); // admin.kelolasertifikasi.report
    });
    Route::prefix('sertifikasi/{sert_id}')->name('sertifikasi.')->group(function () {
        // untuk munculin halaman edit asesmen dan updatenya
        Route::get('/assessment/edit', [AsesmenController::class, 'edit'])->name('assessment.edit'); // admin.sertifikasi.assessment.edit
        Route::patch('/assessment/update', [AsesmenController::class, 'update_tugas_asesmen'])->name('assessment.update'); // admin.sertifikasi.assessment.update
        Route::get('/{asesi_id}/rincian-assessment-asesi/index', [AsesmenController::class, 'rincian_asesmen_asesi'])->name('rincian.assessment.asesi.index'); // admin.sertifikasi.rincian.assessment.asesi.index
        // untuk mnunculin halaman edit rincian pembayaran dan updatenya
        Route::get('/payment-desc/index', [PembayaranController::class, 'index_rincian_pembayaran'])->name('payment-desc.index'); // admin.sertifikasi.payment-desc.index
        Route::patch('/payment-desc/update', [PembayaranController::class, 'update_rincian_pembayaran'])->name('payment-desc.update'); // admin.sertifikasi.payment-desc.update
        // Mengelola konten pengumuman
        Route::get('/assessment-announcement/index', [PengumumanController::class, 'index_pengumuman_asesmen'])->name('assessment-announcement.index'); // admin.sertifikasi.assessment-announcement.index
        Route::post('/assessment-announcement/store', [PengumumanController::class, 'store_pengumuman_asesmen'])->name('assessment-announcement.store'); // admin.sertifikasi.assessment-announcement.store
        Route::patch('/assessment-announcement/update/{peng_id}', [PengumumanController::class, 'update_pengumuman_asesmen'])->name('assessment-announcement.update'); // admin.sertifikasi.assessment-announcement.update
        Route::delete('/assessment-announcement/destroy/{peng_id}', [PengumumanController::class, 'destroy_pengumuman_asesmen'])->name('assessment-announcement.destroy'); // admin.sertifikasi.assessment-announcement.update
        Route::delete('/assessment-announcement/file/{id_file}', [PengumumanController::class, 'destroyPengumumanFile'])->name('assessment-announcement.file.destroy');
    });

    Route::prefix('sertifikasi/{sert_id}')->name('sertifikasi.')->group(function () {
        Route::get('/pendaftar/index', [PendaftarController::class, 'list_asesi'])->name('pendaftar.index'); // admin.sertifikasi.pendaftar.index
        Route::get('/{asesi_id}/pendaftar/show', [PendaftarController::class, 'rincian_data_asesi'])->name('pendaftar.show'); // admin.sertifikasi.pendaftar.show
        Route::patch('/{asesi_id}/pendaftar/update-status', [PendaftarController::class, 'update_status_asesi'])->name('pendaftar.update-status'); // admin.sertifikasi.pendaftar.update-status
        Route::patch('/{transaction_id}/pendaftar/update-payment-status', [PendaftarController::class, 'update_status_pembayaran'])->name('pendaftar.update-payment-status'); // admin.sertifikasi.pendaftar.update-payment-status
        Route::patch('/{asesi_id}/pendaftar/upload-certificate/patch', [PendaftarController::class, 'upload_certificate'])->name('pendaftar.upload-certificate.update'); // admin.sertifikasi.pendaftar.upload-certificate.update
    });
});


//versi lebih baik utk asesi, konsistensi bahasa, penggunaan kebab-case, pakai konvensi restful (index, create, store, show, edit, update, destroy), kelompokin route dgn prefix
Route::middleware(['auth', 'role:asesi'])->prefix('asesi')->name('asesi.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Asesi/DashboardAsesi');
    })->name('dashboard'); // asesi.dashboard

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
        Route::post('{student_id}/apply/store', [KelolaSertifikasiAsesiController::class, 'submit_form_daftar_sertifikasi'])->name('apply.store'); // asesi.sertifikasi.apply.store
        Route::get('/{sert_id}/{asesi_id}/applied/show', [KelolaSertifikasiAsesiController::class, 'detail_applied_sertifikasi'])->name('applied.show'); // asesi.sertifikasi.applied.show
        Route::patch('/{sert_id}/{asesi_id}/applied/update', [KelolaSertifikasiAsesiController::class, 'update_applied_sertifikasi'])->name('applied.update'); // asesi.sertifikasi.applied.update
    });
    Route::get('/{sert_id}/{asesi_id}/pengumuman/index', [PengumumanAsesiController::class, 'index_pengumuman_asesi'])->name('pengumuman.index'); // asesi.pengumuman.index

});

// Route untuk menerima notifikasi (webhook) dari Midtrans.
// Route ini harus di luar middleware 'auth' karena diakses oleh server Midtrans.
// Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook'])->name('midtrans.webhook');
require __DIR__ . '/auth.php';
