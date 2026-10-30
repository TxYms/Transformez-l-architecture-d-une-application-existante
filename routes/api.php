<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\PasswordResetController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');

        Route::middleware('throttle:6,1')->group(function () {
            Route::post('forgot-password', [PasswordResetController::class, 'forgot'])->name('forgot-password');
            Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('reset-password');
        });

        Route::post('logout', [AuthController::class, 'logout'])
            ->middleware('auth:sanctum')
            ->name('logout');
    });

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::get('user', [UserController::class, 'show'])->name('user.show');
        Route::patch('user', [UserController::class, 'update'])->name('user.update');
        Route::put('user/password', [UserController::class, 'updatePassword'])->name('user.password');
        Route::delete('user', [UserController::class, 'destroy'])->name('user.destroy');

        Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
        Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
        Route::get('notes/{id}', [NoteController::class, 'show'])->whereNumber('id')->name('notes.show');
        Route::put('notes/{id}', [NoteController::class, 'update'])->whereNumber('id')->name('notes.update');
        Route::delete('notes/{id}', [NoteController::class, 'destroy'])->whereNumber('id')->name('notes.destroy');

        Route::get('tags', [TagController::class, 'index'])->name('tags.index');
        Route::post('tags', [TagController::class, 'store'])->name('tags.store');
    });
});
