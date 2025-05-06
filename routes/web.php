<?php

use App\Http\Controllers\AnotherController;
use App\Http\Controllers\AsesiController;
use App\Http\Controllers\AsesorController;
use App\Http\Controllers\SkemaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SertificationController;
use App\Models\Sertification;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;


Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified','role:asesi'])->name('dashboard');
Route::get('/dashboardadmin', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified','role:admin,asesor'])->name('dashboardadmin');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile_asesi', [ProfileController::class, 'edit_asesi'])->name('profile_asesi.edit');
    Route::patch('/profile_asesi', [ProfileController::class, 'update_asesi'])->name('profile_asesi.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); 
});
Route::middleware(['auth','role:admin,asesor'])->group(function () {
    Route::resource('/skema',SkemaController::class);
    Route::resource('/asesor',AsesorController::class);
    Route::resource('/sertification',SertificationController::class);
});

Route::middleware(['auth', 'role:asesi'])->group(function () {
    Route::get('/sertification-asesi', function () {
        return view('asesi.sertifikasi.index',[
            'sertifications'=>Sertification::with('skema','asesor')->get()
        ]);
    });
    Route::resource('/asesi',AsesiController::class);
    Route::get('/apply_sertifikasi/{id}',[AnotherController::class, 'apply_sertifikasi'])->name('apply_sertifikasi');
});

require __DIR__.'/auth.php';
