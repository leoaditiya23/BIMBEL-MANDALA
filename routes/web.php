<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes - Mandala Bimbel (Final Sync & Verified)
|--------------------------------------------------------------------------
*/

/**
 * 1. RUTE PUBLIK
 */
Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/faq', [PageController::class, 'faq'])->name('faq.index');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('about');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');

// PROSES KIRIM PESAN
Route::post('/kontak/kirim', [PageController::class, 'storeMessage'])->name('contact.store');

// JEMBATAN PENDAFTARAN
Route::get('/pendaftaran/lanjut', [PageController::class, 'pendaftaranLanjut'])->name('pendaftaran.lanjut');

Route::prefix('program')->group(function () {
    Route::get('/reguler', [PageController::class, 'reguler'])->name('program.reguler');
    Route::get('/intensif', [PageController::class, 'intensif'])->name('program.intensif');
});

/**
 * 2. RUTE GUEST (Belum Login)
 */
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [PageController::class, 'login'])->name('login');
    Route::post('/login', [PageController::class, 'authenticate'])->name('login.store');
    Route::get('/daftar', [PageController::class, 'register'])->name('register');
    Route::post('/daftar', [PageController::class, 'registerStore'])->name('register.store');
    
    // --- FITUR LUPA PASSWORD (LENGKAP) ---
    // 1. Minta Link Reset (Input Email)
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    
    // 2. Form Reset (Setelah Klik Link di Email)
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

/**
 * 3. RUTE AUTH (Wajib Login)
 */
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    // Proses Pendaftaran & Upload Bukti (Siswa)
    Route::post('/enroll-program', [PageController::class, 'enrollProgram'])->name('enroll.program');

    /**
     * --- FITUR ADMIN ---
     */
    Route::prefix('admin')->group(function () {
        Route::get('/overview', [PageController::class, 'adminOverview'])->name('admin.overview');
        Route::get('/programs', [PageController::class, 'adminPrograms'])->name('admin.programs');
        Route::get('/mentors', [PageController::class, 'adminMentors'])->name('admin.mentors');
        Route::get('/payments', [PageController::class, 'adminPayments'])->name('admin.payments');
        Route::get('/settings', [PageController::class, 'adminSettings'])->name('admin.settings');
        
        Route::get('/messages', [PageController::class, 'adminMessages'])->name('admin.messages');
        Route::delete('/messages/{id}', [PageController::class, 'deleteMessage'])->name('admin.messages.delete');

        // Payment Verification & Reject
        Route::post('/verify-payment/{id}', [PageController::class, 'verifyEnrollment'])->name('admin.payments.verify');
        Route::delete('/reject-payment/{id}', [PageController::class, 'rejectPayment'])->name('admin.payments.reject');

        // Program Management
        Route::post('/programs/store', [PageController::class, 'storeProgram'])->name('admin.programs.store');
        Route::put('/programs/update/{id}', [PageController::class, 'updateProgram'])->name('admin.programs.update');
        Route::delete('/programs/delete/{id}', [PageController::class, 'deleteProgram'])->name('admin.programs.delete');

        // Mentor Management
        Route::post('/mentors/store', [PageController::class, 'storeMentor'])->name('admin.mentors.store');
        Route::put('/mentors/update/{id}', [PageController::class, 'updateMentor'])->name('admin.mentors.update');
        Route::delete('/mentors/delete/{id}', [PageController::class, 'deleteMentor'])->name('admin.mentors.delete');
    });

    /**
     * --- FITUR MENTOR ---
     */
    Route::prefix('mentor')->group(function () {
        Route::get('/overview', [PageController::class, 'mentorOverview'])->name('mentor.overview');
        Route::get('/classes', [PageController::class, 'mentorClasses'])->name('mentor.classes');
        Route::get('/schedule', [PageController::class, 'mentorSchedule'])->name('mentor.schedule');
        
        // CREATE / STORE ACTIONS
        Route::post('/assignments/store', [PageController::class, 'storeAssignment'])->name('mentor.assignments.store');
        Route::post('/materials/store', [PageController::class, 'storeMaterial'])->name('mentor.storeMaterial');
        Route::post('/grades/store', [PageController::class, 'storeGrade'])->name('mentor.storeGrade');
        Route::post('/attendance/store', [PageController::class, 'storeAttendance'])->name('mentor.storeAttendance');

        // DELETE ACTIONS
        Route::delete('/materials/delete/{id}', [PageController::class, 'deleteMaterial'])->name('mentor.materials.delete');
        Route::delete('/assignments/delete/{id}', [PageController::class, 'deleteAssignment'])->name('mentor.assignments.delete');
    });

    /**
     * --- FITUR SISWA ---
     */
    Route::prefix('siswa')->group(function () {
        Route::get('/overview', [PageController::class, 'siswaOverview'])->name('siswa.overview');
        Route::get('/programs', [PageController::class, 'siswaPrograms'])->name('siswa.programs');
        Route::get('/schedule', [PageController::class, 'siswaSchedule'])->name('siswa.schedule');
        Route::get('/billing', [PageController::class, 'siswaBilling'])->name('siswa.billing');
    });

    Route::post('/logout', [PageController::class, 'logout'])->name('logout');
});