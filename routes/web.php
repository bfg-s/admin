<?php

Route::page('/', [\Admin\Http\Controllers\DashboardController::class, 'index'])
    ->name('home');

Route::page('login', [\Admin\Http\Controllers\AuthController::class, 'loginForm'])
    ->name('login');

Route::get('logout', [\Admin\Http\Controllers\AuthController::class, 'logout'])
    ->name('logout');
