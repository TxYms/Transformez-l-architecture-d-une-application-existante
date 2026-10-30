<?php

use Illuminate\Support\Facades\Route;

Route::view('/reset-password/{token}', 'app')->name('password.reset');
Route::view('/login', 'app')->name('login');
Route::view('/{any?}', 'app')->where('any', '^(?!api).*$');
