<?php

Route::get('login', [\Admin\Http\Controllers\AuthController::class, 'loginForm'])
    ->name('login');

Route::get('logout', [\Admin\Http\Controllers\AuthController::class, 'logout'])
    ->name('logout');

Route::get('/', [\Admin\Http\Controllers\DashboardController::class, 'index'])
    ->name('home');