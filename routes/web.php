<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\InternshipRequestController as AdminInternshipRequestController;
use App\Http\Controllers\Admin\InternController as AdminInternController;
use App\Http\Controllers\Admin\GroupController as AdminGroupController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\RankingController as AdminRankingController;
use App\Http\Controllers\Admin\TimetableController as AdminTimetableController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Intern\InternAuthController;
use App\Http\Controllers\Intern\InternDashboardController;
use App\Http\Controllers\Intern\TaskController as InternTaskController;
use App\Http\Controllers\Intern\CertificateController as InternCertificateController;

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Landing page with ADMIN / INTERN buttons
Route::get('/', [HomeController::class, 'index'])->name('home');

// Intern side - registration & login
Route::prefix('intern')->name('intern.')->group(function () {
    Route::get('/', [InternAuthController::class, 'showLanding'])->name('landing');
    Route::get('/register', [InternAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [InternAuthController::class, 'register'])->name('register.submit');
    Route::get('/login', [InternAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [InternAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [InternAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'isIntern'])->group(function () {
        Route::get('/dashboard', [InternDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [InternDashboardController::class, 'profile'])->name('profile');
        Route::get('/tasks', [InternTaskController::class, 'index'])->name('tasks.index');
        Route::post('/tasks/{task}/status', [InternTaskController::class, 'markCompleted'])->name('tasks.status');
        Route::get('/certificate', [InternCertificateController::class, 'show'])->name('certificate.show');
        Route::get('/certificate/download', [InternCertificateController::class, 'download'])->name('certificate.download');
    });
});

// Admin auth + protected area
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::middleware(['auth', 'isAdmin'])->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Internship requests management
        Route::get('/requests', [AdminInternshipRequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/{request}', [AdminInternshipRequestController::class, 'show'])->name('requests.show');
        Route::post('/requests/{request}/accept', [AdminInternshipRequestController::class, 'accept'])->name('requests.accept');
        Route::post('/requests/{request}/reject', [AdminInternshipRequestController::class, 'reject'])->name('requests.reject');

        // Intern management
        Route::get('/interns', [AdminInternController::class, 'index'])->name('interns.index');
        Route::get('/interns/{intern}', [AdminInternController::class, 'show'])->name('interns.show');
        Route::get('/interns/{intern}/edit', [AdminInternController::class, 'edit'])->name('interns.edit');
        Route::put('/interns/{intern}', [AdminInternController::class, 'update'])->name('interns.update');
        Route::delete('/interns/{intern}', [AdminInternController::class, 'destroy'])->name('interns.destroy');
        Route::get('/interns-history', [AdminInternController::class, 'history'])->name('interns.history');
        Route::get('/interns-history/export', [AdminInternController::class, 'exportHistory'])->name('interns.history.export');
        Route::post('/interns/{intern}/certificate/draft', [AdminCertificateController::class, 'draft'])->name('interns.certificate.draft');
        Route::post('/interns/{intern}/certificate', [AdminCertificateController::class, 'store'])->name('interns.certificate.store');
        Route::get('/interns/{intern}/certificate/download', [AdminCertificateController::class, 'download'])->name('interns.certificate.download');

        // Groups management
        Route::resource('groups', AdminGroupController::class)->only(['index', 'show']);

        // Task management
        Route::get('/tasks', [AdminTaskController::class, 'index'])->name('tasks.index');
        Route::post('/tasks', [AdminTaskController::class, 'store'])->name('tasks.store');
        Route::put('/tasks/{task}', [AdminTaskController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [AdminTaskController::class, 'destroy'])->name('tasks.destroy');
        Route::get('/tasks/status-updates', [AdminTaskController::class, 'statusUpdates'])->name('tasks.status');

        // Timetables
        Route::get('/timetables', [AdminTimetableController::class, 'index'])->name('timetables.index');
        Route::get('/timetables/create', [AdminTimetableController::class, 'create'])->name('timetables.create');
        Route::post('/timetables', [AdminTimetableController::class, 'store'])->name('timetables.store');
        Route::put('/timetables/{timetable}', [AdminTimetableController::class, 'update'])->name('timetables.update');
        Route::delete('/timetables/{timetable}', [AdminTimetableController::class, 'destroy'])->name('timetables.destroy');

        // Attendance
        Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/mark', [AdminAttendanceController::class, 'mark'])->name('attendance.mark');
        });
});
