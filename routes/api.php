<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Api\ProfileController; // << PASTIKAN IMPORT INI ADA
use App\Http\Controllers\Api\TopicController;              // << TAMBAHKAN IMPORT INI
use App\Http\Controllers\Api\UserPreferenceController;
use App\Http\Controllers\Api\LearningHistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route Publik (tidak memerlukan autentikasi)
Route::post('/register', [RegisteredUserController::class, 'store'])
                ->middleware('guest')
                ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                ->middleware('guest')
                ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware('guest')
                ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.store');
Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');

// Route yang memerlukan autentikasi (Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    // Mengarahkan GET /user ke ProfileController@show
    Route::get('/user', [ProfileController::class, 'show']); // << MODIFIKASI DI SINI

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    // Rute untuk update profil, ubah password, dll akan ditambahkan di sini:
    Route::post('/user/profile', [ProfileController::class, 'update']);
    Route::post('/user/password', [ProfileController::class, 'updatePassword']);
    Route::post('/user/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('/user', [ProfileController::class, 'destroyAccount']);
    Route::post('/user/password', [ProfileController::class, 'updatePassword']);
    Route::get('/user/preferences', [UserPreferenceController::class, 'index'])->name('user.preferences.index');
    Route::post('/user/preferences', [UserPreferenceController::class, 'store'])->name('user.preferences.store');
    Route::get('/user/learning-history', [LearningHistoryController::class, 'index'])->name('user.learning-history.index');
    Route::post('/materials/{material}/complete', [LearningHistoryController::class, 'store'])->name('materials.complete');
    // Jika Anda menggunakan fitur verifikasi email (opsional untuk sekarang):
    // Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    //             ->middleware(['signed', 'throttle:6,1'])
    //             ->name('verification.verify');
    // Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    //             ->middleware(['throttle:6,1'])
    //             ->name('verification.send');
});