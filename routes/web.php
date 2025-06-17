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
        return view('admin.dashboardadmin');
    })->name('dashboardadmin');
    Route::get('/manage_skema', [ManageSkemaController::class, 'create'])->name('manage_skema');
    Route::post('/manage_skema', [ManageSkemaController::class, 'store'])->name('manage_skema');
    Route::get('/manage_skema/{id}/edit', [ManageSkemaController::class, 'edit'])->name('manage_skema.edit');
    Route::patch('/manage_skema/{id}/update', [ManageSkemaController::class, 'update'])->name('manage_skema.update');
    Route::delete('/manage_skema/{id}/destroy', [ManageSkemaController::class, 'destroy'])->name('manage_skema.destroy');
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
    Route::get('/sertification-asesi', [ManageCertificationController::class, 'asesi_index_sertifikasi']);
    // route asesi buat store dari apply_page, update dari edit_apply_page
    Route::get('/apply_sertifikasi/{id}', [ManageCertificationController::class, 'apply_sertifikasi'])->name('apply_sertifikasi');
    Route::post('/apply_sertifikasi', [ApplyCertificationController::class, 'store'])->name('apply_sertifikasi.store');
    Route::get('/show_applied_sertifikasi/{sert_id}/{asesi_id}', [ManageCertificationController::class, 'show_applied_sertifikasi'])->name('show_applied_sertifikasi');
    Route::get('/edit_apply_sertifikasi/{sert_id}/{asesi_id}', [ManageCertificationController::class, 'edit_apply_sertifikasi'])->name('edit_apply_sertifikasi');
    Route::patch('/update_apply_sertifikasi/{asesi_id}/update', [ApplyCertificationController::class, 'update'])->name('update_apply_sertifikasi.update');
    // route asesi buat akses praasesmen
    Route::get('/rincian_praasesmen_asesi/{id}', [ManageCertificationController::class, 'rincian_praasesmen_asesi'])->name('rincian_praasesmen_asesi');
    // route asesi buat akses asesmen dan ngumpulin asesmen
    Route::get('/rincian_asesmen_asesi/{id}', [ManageCertificationController::class, 'rincian_asesmen_asesi'])->name('rincian_asesmen_asesi');
    Route::post('/asesmen_asesi/{id}', [ManageCertificationController::class, 'store_asesmen_asesi'])->name('asesmen_asesi.store');
    // payment routes
    Route::post('/payment', [ManageCertificationController::class, 'rincian_bayar_asesi']);
    Route::post('/checkout', [PaymentController::class, 'checkout']);
    Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook']);
});
// Route::group(['prefix' => 'asesi/dropzone', 'middleware' => ['auth', 'role:asesi']], function () {
//     Route::post('upload', [ManageCertificationController::class, 'asesi_apply_sertif_dropzone_upload'])->name('asesi.dropzone.upload');
//     Route::delete('remove', [ManageCertificationController::class, 'asesi_apply_sertif_dropzone_remove'])->name('asesi.dropzone.remove');
// });

require __DIR__ . '/auth.php';
