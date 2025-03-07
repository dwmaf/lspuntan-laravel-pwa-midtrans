<?php

use App\Http\Controllers\AsesiController;
use App\Http\Controllers\AsesorController;
use App\Http\Controllers\SkemaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SertificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;


Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboardadmin', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified','role:admin,asesor'])->name('dashboardadmin');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('/asesi',AsesiController::class);
    Route::resource('/skema',SkemaController::class);
    Route::resource('/asesor',AsesorController::class);
    Route::resource('/sertification',SertificationController::class);
    // Route::get('/sertification/index_asesi',SertificationController::class, 'index_asesi');
});

require __DIR__.'/auth.php';
