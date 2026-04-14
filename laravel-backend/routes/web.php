<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MigrationController;

Route::post('/api/migrate-firebase', [MigrationController::class, 'importFirebase'])->name('api.migrate');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::get('/reports/staff', [ReportController::class, 'staff'])->name('reports.staff');
    Route::get('/reports/{report}/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
});

use App\Http\Controllers\AdminUserController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::put('/admin/users/{user}/password', [AdminUserController::class, 'updatePassword'])->name('admin.users.password');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});
