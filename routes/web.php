<?php

use App\Http\Controllers\ManageCertificationController;
use App\Http\Controllers\AsesiController;
use App\Http\Controllers\AsesorController;
use App\Http\Controllers\SkemaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SertificationController;
use App\Http\Controllers\PaymentController;
use App\Models\Sertification;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;


Route::get('/', function () {
    return view('auth.login');
});




// punya semua user
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile_asesi', [ProfileController::class, 'edit_asesi'])->name('profile_asesi.edit');
    Route::patch('/profile_asesi', [ProfileController::class, 'update_asesi'])->name('profile_asesi.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); 
});

//punya admin dan asesor
Route::middleware(['auth', 'role:admin,asesor'])->group(function () {
    Route::get('/dashboardadmin', function () {
        return view('admin.dashboard');
    })->name('dashboardadmin');
    Route::resource('/skema', SkemaController::class);
    Route::resource('/asesor', AsesorController::class);
    Route::resource('/sertification', SertificationController::class);
    Route::get('/list_asesi/{id}', [ManageCertificationController::class, 'list_asesi'])->name('list_asesi');
    Route::get('/rincian_data_asesi/{id}', [ManageCertificationController::class, 'rincian_data_asesi'])->name('rincian_data_asesi');
    Route::patch('/updatestatus/{id}/{sertification_id}', [ManageCertificationController::class, 'updateStatus'])->name('updatestatus');
    Route::get('/rincian_praasesmen/{id}/edit', [ManageCertificationController::class, 'rincian_praasesmen'])->name('rincian_praasesmen.edit');
    Route::patch('/rincian_praasesmen/{id}/update', [ManageCertificationController::class, 'update_rincian_praasesmen'])->name('rincian_praasesmen.update');
    Route::get('/rincian_asesmen/{id}/edit', [ManageCertificationController::class, 'rincian_asesmen'])->name('rincian_asesmen.edit');
    Route::patch('/rincian_asesmen/{id}/update', [ManageCertificationController::class, 'update_rincian_asesmen'])->name('rincian_asesmen.update');
});

//punya asesi
Route::middleware(['auth', 'role:asesi'])->group(function () {
    Route::get('/dashboard', function () {
        return view('asesi.dashboardasesi');
    })->name('dashboard');
    Route::get('/sertification-asesi', function () {
        return view('asesi.sertifikasi.asesi-index-sertifikasi', [
            'sertifications' => Sertification::with('skema', 'asesor')->get(),
        ]);
    });
    Route::resource('/asesi', AsesiController::class);
    Route::get('/apply_sertifikasi/{id}', [ManageCertificationController::class, 'apply_sertifikasi'])->name('apply_sertifikasi');
    Route::get('/edit_apply_sertifikasi/{sert_id}/{asesi_id}', [ManageCertificationController::class, 'edit_apply_sertifikasi'])->name('edit_apply_sertifikasi');
    Route::get('/rincian_praasesmen_asesi/{id}', [ManageCertificationController::class, 'rincian_praasesmen_asesi'])->name('rincian_praasesmen_asesi');
    // payment routes
    Route::post('/payment', [ManageCertificationController::class, 'rincian_bayar_asesi']);
    Route::post('/checkout', [PaymentController::class, 'checkout']);
    Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook']);
});

require __DIR__ . '/auth.php';
