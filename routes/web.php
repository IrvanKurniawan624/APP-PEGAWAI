<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SalaryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::get('/register', [AuthController::class, 'register_view'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::view('/lost', 'errors.404')->name('lost');

Route::middleware(['CekLogin'])->group(function () {

    Route::get('/', function () {
        return Redirect::route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('employees', EmployeeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('positions', PositionController::class);

    Route::prefix('attendance')->group(function () {
        Route::get('admin', [AttendanceController::class, 'adminList'])->name('attendance.admin');
        Route::post('admin/update-status', [AttendanceController::class, 'updateStatus'])->name('attendance.update.status');
        Route::post('admin/delete-checkin', [AttendanceController::class, 'deleteCheckIn'])->name('attendance.delete.in');
        Route::post('admin/delete-checkout', [AttendanceController::class, 'deleteCheckOut'])->name('attendance.delete.out');

        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.karyawan');
        Route::post('export/pdf', [AttendanceController::class, 'exportPdf'])->name('attendance.export.pdf');

        Route::post('check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
        Route::post('check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    });

    Route::prefix('permission')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permission.karyawan');
        Route::post('upload', [PermissionController::class, 'upload'])->name('permission.upload');
        Route::post('create', [PermissionController::class, 'create'])->name('permission.create');

        Route::get('admin', [PermissionController::class, 'admin'])->name('permission.admin');
        Route::post('approve', [PermissionController::class, 'approve'])->name('permission.approve');
        Route::post('reject', [PermissionController::class, 'reject'])->name('permission.reject');
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.karyawan');
        Route::post('update-all', [ProfileController::class, 'updateAll'])->name('profile.update.all');
    });

    Route::resource('announcement', AnnouncementController::class);


    Route::get('/salary/admin', [SalaryController::class, 'admin'])
    ->name('salary.admin');

    Route::post('/salary/preview', [SalaryController::class, 'preview'])
        ->name('salary.preview');

    Route::post('/salary/generate', [SalaryController::class, 'generate'])
        ->name('salary.generate');

    Route::get('/salary/slip-saya', [SalaryController::class, 'slipKaryawan'])
        ->name('salary.karyawan');

    Route::get('/salary/download/{id}', [SalaryController::class, 'downloadPdf'])
        ->name('salary.pdf');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
