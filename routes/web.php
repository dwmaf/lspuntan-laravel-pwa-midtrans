<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\Sertifikasi\AsesmenController;
use App\Http\Controllers\Admin\Sertifikasi\KelolaSertifikasiController;
use App\Http\Controllers\Admin\Sertifikasi\PembayaranController;
use App\Http\Controllers\Admin\Sertifikasi\PendaftarController;
use App\Http\Controllers\Admin\Sertifikasi\PengumumanController;
use App\Http\Controllers\Admin\SkemaController;
use App\Http\Controllers\Asesi\Sertifikasi\PembayaranAsesiController;
use App\Http\Controllers\Asesi\Sertifikasi\AsesmenAsesiController;
use App\Http\Controllers\Asesi\Sertifikasi\KelolaSertifikasiAsesiController;
use App\Http\Controllers\Asesi\DashboardAsesiController;
use App\Http\Controllers\Admin\AsesorController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Asesi\Sertifikasi\PengumumanAsesiController;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\Dev\DevelopmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfflineController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\FilePondTestController;
use App\Models\Sertification;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Vite;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canResetPassword' => Route::has('password.request'),
        'status' => session('status'),
    ]);
})->middleware('guest');


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
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/fcm/token', [FcmController::class, 'saveToken'])->name('fcm.token');
    Route::delete('/fcm/token', [FcmController::class, 'removeToken'])->name('fcm.token.remove');
    Route::get('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});


//nanti harus dikasih middleware 'verified'
Route::middleware(['auth', 'role:admin|asesor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard'); //admin.dashboard

    Route::prefix('skema')->name('skema.')->group(function () {
        Route::get('/', [SkemaController::class, 'create'])->name('create'); // admin.skema.create
        Route::post('/', [SkemaController::class, 'store'])->name('store'); // admin.skema.store
        Route::patch('/{skema}/update', [SkemaController::class, 'update'])->name('update'); // admin.skema.update
        Route::delete('/{skema}/destroy', [SkemaController::class, 'destroy'])->name('destroy'); // admin.skema.destroy
    });
    Route::prefix('asesor')->name('asesor.')->group(function () {
        Route::get('/index', [AsesorController::class, 'index'])->name('index'); // admin.asesor.index
        Route::get('/export', [AsesorController::class, 'export'])->name('export'); // admin.asesor.export
        Route::post('/', [AsesorController::class, 'store'])->name('store'); // admin.asesor.store
        Route::patch('/{asesor}/update', [AsesorController::class, 'update'])->name('update'); // admin.asesor.update
        Route::delete('/{asesor}/destroy', [AsesorController::class, 'destroy'])->name('destroy'); // admin.asesor.destroy
        Route::patch('/{id}/restore', [AsesorController::class, 'restore'])->name('restore'); // admin.asesor.restore
    });
    Route::prefix('kelolasertifikasi')->name('kelolasertifikasi.')->group(function () {
        Route::get('/index', [KelolaSertifikasiController::class, 'index'])->name('index'); // admin.kelolasertifikasi.index
        Route::post('/store', [KelolaSertifikasiController::class, 'store'])->name('store'); // admin.kelolasertifikasi.store
        Route::get('/{sertification}/show', [KelolaSertifikasiController::class, 'show'])->name('show'); // admin.kelolasertifikasi.show
        Route::patch('/{sertification}/update', [KelolaSertifikasiController::class, 'update'])->name('update'); // admin.kelolasertifikasi.update
        Route::patch('/{sertification}/cancel', [KelolaSertifikasiController::class, 'cancel'])->name('cancel'); // admin.kelolasertifikasi.cancel
        Route::get('/{sertification}/report/export-excel', [KelolaSertifikasiController::class, 'export_excel'])->name('report.export_excel');
    });
    Route::prefix('sertifikasi/{sertification}')->name('sertifikasi.')->group(function () {
        // untuk munculin halaman edit asesmen dan updatenya
        Route::get('/assessment/edit', [AsesmenController::class, 'edit'])->name('assessment.edit'); // admin.sertifikasi.assessment.edit
        Route::patch('/assessment/update', [AsesmenController::class, 'update_tugas_asesmen'])->name('assessment.update'); // admin.sertifikasi.assessment.update
        Route::delete('/assessment/destroy', [AsesmenController::class, 'destroy'])->name('assessment.destroy'); // admin.sertifikasi.assessment.destroy
        Route::get('/announcement/index', [PengumumanController::class, 'index_pengumuman_asesmen'])->name('assessment-announcement.index'); // admin.sertifikasi.assessment-announcement.index
        Route::post('/announcement/store', [PengumumanController::class, 'store_pengumuman_asesmen'])->name('announcement.store'); // admin.sertifikasi.assessment-announcement.store
        Route::patch('/announcement/update/{news}', [PengumumanController::class, 'update_pengumuman_asesmen'])->name('assessment-announcement.update'); // admin.sertifikasi.assessment-announcement.update
        Route::delete('/announcement/destroy/{news}', [PengumumanController::class, 'destroy_pengumuman_asesmen'])->name('assessment-announcement.destroy'); // admin.sertifikasi.assessment-announcement.update
        Route::delete('/announcement/file/{id_file}', [PengumumanController::class, 'destroyPengumumanFile'])->name('assessment-announcement.file.destroy');
        Route::get('/announcement/{news}/readers', [PengumumanController::class, 'getReaders'])->name('assessment-announcement.readers');
    });

    Route::prefix('sertifikasi/{sertification}')->name('sertifikasi.')->group(function () {
        Route::get('/pendaftar/index', [PendaftarController::class, 'listAsesi'])->name('pendaftar.index'); // admin.sertifikasi.pendaftar.index
        Route::get('/pendaftar/{asesi}/show', [PendaftarController::class, 'rincianDataAsesi'])->name('pendaftar.show'); // admin.sertifikasi.pendaftar.show
        Route::patch('/pendaftar/status-berkas/bulk', [PendaftarController::class, 'updateStatusBerkasBulk'])->name('pendaftar.update-status-berkas-bulk');
        Route::patch('/pendaftar/update-akses-asesmen/bulk', [PendaftarController::class, 'updateAksesAsesmenBulk'])->name('pendaftar.update-akses-asesmen-bulk');
        Route::patch('/pendaftar/update-status-final-asesi/bulk', [PendaftarController::class, 'updateStatusFinalBulk'])->name('pendaftar.update-status-final-bulk');
        Route::patch('/pendaftar/{asesi}/status-berkas', [PendaftarController::class, 'updateStatusBerkas'])->name('pendaftar.update-status-berkas'); // admin.sertifikasi.pendaftar.update-status-berkas-asesi
        Route::patch('/pendaftar/{asesi}/update-akses-asesmen', [PendaftarController::class, 'updateAksesAsesmen'])->name('pendaftar.update-akses-asesmen'); // admin.sertifikasi.pendaftar.update-akses-menu-asesmen
        Route::patch('/pendaftar/{asesi}/update-status-final-asesi', [PendaftarController::class, 'updateStatusFinal'])->name('pendaftar.update-status-final'); // admin.sertifikasi.pendaftar.update-status-final-asesi
        Route::patch('/pendaftar/{asesi}/update-certificate', [PendaftarController::class, 'updateCertificate'])->name('pendaftar.update-certificate'); // admin.sertifikasi.pendaftar.upload-certificate
        Route::patch('/pendaftar/{asesi}/update-apl', [PendaftarController::class, 'updateApl'])->name('pendaftar.update-apl'); // admin.sertifikasi.pendaftar.upload-apl
    });
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::patch('/{user}', [UserController::class, 'update'])->name('update');
        Route::post('/{user}/ban', [UserController::class, 'ban'])->name('ban');
    });
});


//versi lebih baik utk asesi, konsistensi bahasa, penggunaan kebab-case, pakai konvensi restful (index, create, store, show, edit, update, destroy), kelompokin route dgn prefix
Route::middleware(['auth', 'role:asesi'])->prefix('asesi')->name('asesi.')->group(function () {
    Route::get('/dashboard', [DashboardAsesiController::class, 'index'])->name('dashboard'); // asesi.dashboard

    // Grouping sertifikasi context for asesi
    Route::prefix('sertifikasi/{sertification}/{asesi}')->group(function () {
        Route::get('/assessmen/index', [AsesmenAsesiController::class, 'index'])->name('assessmen.index'); 
        Route::post('/assessmen', [AsesmenAsesiController::class, 'update'])->name('assessmen.update');
        Route::get('/pengumuman/index', [PengumumanAsesiController::class, 'index'])->name('pengumuman.index');
        Route::post('/pengumuman/{news}/read', [PengumumanAsesiController::class, 'markAsRead'])->name('pengumuman.mark-read');
    });

    Route::prefix('sertifikasi')->name('sertifikasi.')->group(function () {
        Route::get('/index', [KelolaSertifikasiAsesiController::class, 'listSertifications'])->name('index'); 
        Route::get('/{sertification}/apply/create', [KelolaSertifikasiAsesiController::class, 'applyForm'])->name('apply.create');
        Route::post('/{student}/apply/store', [KelolaSertifikasiAsesiController::class, 'submitForm'])->name('apply.store');
        Route::get('/{sertification}/{asesi}/applied/show', [KelolaSertifikasiAsesiController::class, 'showApplied'])->name('applied.show');
        Route::patch('/{sertification}/{asesi}/applied/update', [KelolaSertifikasiAsesiController::class, 'updateApplied'])->name('applied.update');
    });
});

// Route untuk menerima notifikasi (webhook) dari Midtrans.
// Route ini harus di luar middleware 'auth' karena diakses oleh server Midtrans.
// Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook'])->name('midtrans.webhook');

// Below will be commented on production
Route::resource('filepond-test', FilePondTestController::class);
Route::post('/filepond/process', [FilePondTestController::class, 'processFile'])->name('filepond.process');
Route::get('/filepond/load', [FilePondTestController::class, 'loadFile'])->name('filepond.load');
Route::delete('/filepond/revert', [FilePondTestController::class, 'revertFile'])->name('filepond.revert');
Route::get('/test', function () { //will be commented on production
    return view('dumpbladefiles.testing-file', [
        'sertification' => Sertification::find(1)
    ]);
});
Route::get('/debug-firebase', function () { //will be commented on production
    try {
        $messaging = app('firebase.messaging');
        dd('Koneksi ke Firebase Messaging BERHASIL! Driver seharusnya sudah terdaftar. Masalah ada di tempat lain.');
    } catch (\Throwable $e) {
        dd($e);
    }
});
Route::prefix('dev')->name('dev.')->group(function () { //will be commented on production
    Route::get('/list/sertifications', [DevelopmentController::class, 'index'])->name('list.sertifications'); // dev.list.sertifications
    Route::prefix('sertification')->name('sertification.')->group(function () {
        Route::get('/{sert_id}/show', [DevelopmentController::class, 'detailSertification'])->name('show'); // dev.sertification.show
        Route::delete('/{sert_id}/destroy/asesmen', [DevelopmentController::class, 'destroyAsesmen'])->name('destroy.asesmen'); // dev.sertification.destroy.asesmen
        Route::post('/{sert_id}/store/news', [DevelopmentController::class, 'storeDummyNews'])->name('store.news'); // dev.sertification.store.news
        Route::delete('/{sert_id}/destroy/news', [DevelopmentController::class, 'destroyNews'])->name('destroy.news'); // dev.sertification.destroy.news
        Route::get('/{sert_id}/list/asesis', [DevelopmentController::class, 'listAsesis'])->name('list.asesis'); // dev.sertification.list.asesis
    });
});
require __DIR__ . '/auth.php';
