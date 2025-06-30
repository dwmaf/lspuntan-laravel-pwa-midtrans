<?php

use App\Http\Controllers\ApplyCertificationController;
use App\Http\Controllers\ManageCertificationController;
use App\Http\Controllers\AsesiController;
use App\Http\Controllers\AsesorController;
use App\Http\Controllers\ManageSkemaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SertificationController;
use App\Http\Controllers\PaymentController;
use App\Models\Sertification;
use App\Models\Asesi;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;


Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');




// punya semua user
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile_asesi', [ProfileController::class, 'edit_asesi'])->name('profile_asesi.edit');
    Route::patch('/profile_asesi', [ProfileController::class, 'update_asesi'])->name('profile_asesi.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); 
});

//punya admin dan asesor
// Route::middleware(['auth', 'role:admin,asesor'])->group(function () {
//     Route::get('/dashboardadmin', function () {
//         return view('admin.dashboardadmin');
//     })->name('dashboardadmin');
//     Route::get('/manage_skema', [ManageSkemaController::class, 'create'])->name('manage_skema');
//     Route::post('/manage_skema', [ManageSkemaController::class, 'store'])->name('manage_skema');
//     Route::get('/manage_skema/{id}/edit', [ManageSkemaController::class, 'edit'])->name('manage_skema.edit');
//     Route::patch('/manage_skema/{id}/update', [ManageSkemaController::class, 'update'])->name('manage_skema.update');
//     Route::delete('/manage_skema/{id}/destroy', [ManageSkemaController::class, 'destroy'])->name('manage_skema.destroy');
//     Route::resource('/asesor', AsesorController::class);
//     Route::resource('/sertification', SertificationController::class);
//     Route::get('/list_asesi/{id}', [ManageCertificationController::class, 'list_asesi'])->name('list_asesi');
//     Route::get('/rincian_data_asesi/{id}', [ManageCertificationController::class, 'rincian_data_asesi'])->name('rincian_data_asesi');
//     Route::patch('/update_status/{id}/{sertification_id}', [ManageCertificationController::class, 'updateStatus'])->name('update_status');
//     Route::patch('/update_status_bayar/{id}', [ManageCertificationController::class, 'updateStatusBayar'])->name('update_status_bayar');
//     Route::get('/rincian_praasesmen/{id}/edit', [ManageCertificationController::class, 'rincian_praasesmen'])->name('rincian_praasesmen.edit');
//     Route::patch('/rincian_praasesmen/{id}/update', [ManageCertificationController::class, 'update_rincian_praasesmen'])->name('rincian_praasesmen.update');
//     Route::get('/rincian_asesmen/{id}/edit', [ManageCertificationController::class, 'rincian_asesmen'])->name('rincian_asesmen.edit');
//     Route::patch('/rincian_asesmen/{id}/update', [ManageCertificationController::class, 'update_rincian_asesmen'])->name('rincian_asesmen.update');
//     Route::delete('/praasesmen-file-ajax-delete', [ManageCertificationController::class, 'ajaxDeletePraasesmenFile'])->name('praasesmen.file.ajaxdelete');
//     Route::delete('/asesmen-file-ajax-delete', [ManageCertificationController::class, 'ajaxDeleteAsesmenFile'])->name('asesmen.file.ajaxdelete');
// });

//versi lebih baik asesor admin
Route::middleware(['auth', 'role:admin,asesor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboardadmin');
    })->name('dashboard'); // Hasil: admin.dashboard

    // Menggunakan Route::resource untuk CRUD standar, lebih bersih.
    Route::prefix('skema')->name('skema')->group(function () {
        Route::get('/', [ManageSkemaController::class, 'create'])->name('create'); // admin.skema
        Route::post('/', [ManageSkemaController::class, 'store'])->name('store'); // admin.skema
        Route::get('/{id}/edit', [ManageSkemaController::class, 'edit'])->name('edit'); // admin.skema.edit
        Route::patch('/{id}/update', [ManageSkemaController::class, 'update'])->name('update'); // admin.skema.update
        Route::delete('/{id}/destroy', [ManageSkemaController::class, 'destroy'])->name('destroy'); // admin.skema.destroy
    });
    Route::resource('asesor', AsesorController::class); // URI: admin/asesor
    Route::resource('sertification', SertificationController::class); // URI: admin/sertification

    // Mengelola konten (pra-asesmen & asesmen) untuk sebuah sertifikasi
    Route::prefix('sertification/{id}')->name('sertification.')->group(function () {
        // Menampilkan daftar asesi/pendaftar untuk sertifikasi ini
        Route::get('/applicants', [ManageCertificationController::class, 'list_asesi'])->name('applicants.index'); // admin.sertification.applicants.index

        // Mengelola konten pra-asesmen
        Route::get('/pre-assessment/edit', [ManageCertificationController::class, 'rincian_praasesmen'])->name('pre-assessment.edit'); // admin.sertification.pre-assessment.edit
        Route::patch('/pre-assessment/update', [ManageCertificationController::class, 'update_rincian_praasesmen'])->name('pre-assessment.update'); // admin.sertification.pre-assessment.update

        // Mengelola konten asesmen
        Route::get('/assessment/edit', [ManageCertificationController::class, 'rincian_asesmen'])->name('assessment.edit'); // admin.sertification.assessment.edit
        Route::patch('/assessment/update', [ManageCertificationController::class, 'update_rincian_asesmen'])->name('assessment.update'); // admin.sertification.assessment.update
    });

    // Mengelola data pendaftar (asesi) secara spesifik
    Route::prefix('applicants/{id}')->name('applicants.')->group(function () {
        Route::get('/', [ManageCertificationController::class, 'rincian_data_asesi'])->name('show'); // admin.applicants.show
        Route::patch('/{sert_id}/update-status', [ManageCertificationController::class, 'updateStatus'])->name('update-status'); // admin.applicants.update-status
        Route::patch('/update-payment-status', [ManageCertificationController::class, 'updateStatusBayar'])->name('update-payment-status'); // admin.applicants.update-payment-status
    });

    // Route untuk AJAX file delete (lebih baik dibuat spesifik)
    // Contoh: Route::delete('/pre-assessment-files/{file}', ...)->name('pre-assessment-files.destroy');
    Route::delete('/praasesmen-file-ajax-delete', [ManageCertificationController::class, 'ajaxDeletePraasesmenFile'])->name('praasesmen.file.ajaxdelete');
    Route::delete('/asesmen-file-ajax-delete', [ManageCertificationController::class, 'ajaxDeleteAsesmenFile'])->name('asesmen.file.ajaxdelete');
});

//punya asesi
// Route::middleware(['auth', 'role:asesi'])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('asesi.dashboardasesi');
//     })->name('dashboard');
//     Route::get('/sertification-asesi', [ManageCertificationController::class, 'asesi_index_sertifikasi']);
//     // route asesi buat store dari apply_page, update dari edit_apply_page
//     Route::get('/apply_sertifikasi/{id}', [ManageCertificationController::class, 'apply_sertifikasi'])->name('apply_sertifikasi');
//     Route::post('/apply_sertifikasi', [ApplyCertificationController::class, 'store'])->name('apply_sertifikasi.store');
//     Route::get('/show_applied_sertifikasi/{sert_id}/{asesi_id}', [ManageCertificationController::class, 'show_applied_sertifikasi'])->name('show_applied_sertifikasi');
//     Route::get('/edit_apply_sertifikasi/{sert_id}/{asesi_id}', [ManageCertificationController::class, 'edit_apply_sertifikasi'])->name('edit_apply_sertifikasi');
//     Route::patch('/update_apply_sertifikasi/{asesi_id}/update', [ApplyCertificationController::class, 'update'])->name('update_apply_sertifikasi.update');
//     // route asesi buat akses praasesmen
//     Route::get('/rincian_praasesmen_asesi/{id}', [ManageCertificationController::class, 'rincian_praasesmen_asesi'])->name('rincian_praasesmen_asesi');
//     // route asesi buat akses asesmen dan ngumpulin asesmen
//     Route::get('/rincian_asesmen_asesi/{id}', [ManageCertificationController::class, 'rincian_asesmen_asesi'])->name('rincian_asesmen_asesi');
//     Route::post('/asesmen_asesi/{id}', [ManageCertificationController::class, 'store_asesmen_asesi'])->name('asesmen_asesi.store');
//     // payment routes
//     Route::get('/payment/{sert_id}/{asesi_id}', [ManageCertificationController::class, 'rincian_bayar_asesi'])->name('payment');
//     Route::post('/checkout/{sert_id}/{asesi_id}', [PaymentController::class, 'checkout'])->name('checkout');
//     Route::post('/payments/manual/{sert_id}/{asesi_id}', [PaymentController::class, 'buktipembayaran'])->name('payments.manual.store');
//     // Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook']);
// });

//versi lebih baik, konsistensi bahasa, penggunaan kebab-case, pakai konvensi restful (index, create, store, show, edit, update, destroy), kelompokin route dgn prefix
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
        Route::get('/pre-assessment', [ManageCertificationController::class, 'rincian_praasesmen_asesi'])->name('pre-assessment.show'); // asesi.applied.pre-assessment.show
        Route::get('/assessment', [ManageCertificationController::class, 'rincian_asesmen_asesi'])->name('assessment.show'); // asesi.applied.assessment.show
        Route::post('/assessment', [ManageCertificationController::class, 'store_asesmen_asesi'])->name('assessment.store'); // asesi.applied.assessment.store

        // Payment Routes
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/{asesi_id}', [ManageCertificationController::class, 'rincian_bayar_asesi'])->name('create'); // asesi.applied.payment.create
            Route::post('{asesi_id}/checkout', [PaymentController::class, 'checkout'])->name('checkout'); // asesi.applied.payment.checkout
            Route::post('{asesi_id}/manual', [PaymentController::class, 'buktipembayaran'])->name('manual.store'); // asesi.applied.payment.manual.store
        });
    });
});

// Route untuk menerima notifikasi (webhook) dari Midtrans.
// Route ini harus di luar middleware 'auth' karena diakses oleh server Midtrans.
Route::post('/webhook/midtrans', [PaymentController::class, 'handleWebhook'])->name('midtrans.webhook');

require __DIR__ . '/auth.php';
