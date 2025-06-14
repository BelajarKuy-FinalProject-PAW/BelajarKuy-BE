<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SubMaterialController;
use App\Http\Controllers\Api\UserPreferenceController;
use App\Http\Controllers\Api\LearningHistoryController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Api\MaterialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route Publik 
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
                
Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');

Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback'])->name('social.callback');

// Route yang memerlukan autentikasi (Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [ProfileController::class, 'show']); 

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    // Rute untuk update profil, ubah password, dll.
    Route::post('/user/profile', [ProfileController::class, 'update']);
    Route::post('/user/password', [ProfileController::class, 'updatePassword']); // <-- HANYA SATU SEKARANG
    Route::post('/user/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('/user', [ProfileController::class, 'destroyAccount']);
    Route::get('/user/preferences', [UserPreferenceController::class, 'index'])->name('user.preferences.index');
    Route::post('/user/preferences', [UserPreferenceController::class, 'store'])->name('user.preferences.store');
    Route::get('/user/learning-history', [LearningHistoryController::class, 'index'])->name('user.learning-history.index');
    Route::post('/materials/{material}/complete', [LearningHistoryController::class, 'store'])->name('materials.complete');
    Route::post('/materials/{material}/sub-materials', [SubMaterialController::class, 'store'])->name('sub-materials.store');
    Route::get('/sub-materials/{sub_material}', [SubMaterialController::class, 'show'])->name('sub-materials.show');
    Route::put('/sub-materials/{sub_material}', [SubMaterialController::class, 'update'])->name('sub-materials.update');
    Route::delete('/sub-materials/{sub_material}', [SubMaterialController::class, 'destroy'])->name('sub-materials.destroy');
});