<?php

use Admin\Controllers\AuthController;
use Admin\Controllers\DashboardController;
use Admin\Controllers\SystemController;
use Admin\Controllers\UploadController;
use Admin\Controllers\UserController;
use Lar\Roads\Roads;
use Admin\Controllers\SettingsController;
use Admin\Core\RoutesAdaptor;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/**
 * Admin Auth routes.
 */
Route::group([], function (Router $route) {
    $route->get('/', [AuthController::class, 'login'])->name('home');
    $route->get('login', [AuthController::class, 'login'])->name('login');
    $route->get('2fa', [AuthController::class, 'twofaGet'])->name('2fa.get');
    $route->post('2fa', [AuthController::class, 'twofa'])->name('2fa');
    $route->post('2fa_post', [AuthController::class, 'twofaPost'])->name('2fa.post');

    $route->post('load_modal', [SystemController::class, 'load_modal'])->name('load_modal');
    $route->post('toggle_dark', [SystemController::class, 'toggle_dark'])->name('toggle_dark');
    $route->post('export_excel', [SystemController::class, 'export_excel'])->name('export_excel');
    $route->post('export_csv', [SystemController::class, 'export_csv'])->name('export_csv');
    $route->post('custom_save', [SystemController::class, 'custom_save'])->name('custom_save');
    $route->post('call_callback', [SystemController::class, 'call_callback'])->name('call_callback');
    $route->post('mass_delete', [SystemController::class, 'mass_delete'])->name('mass_delete');
    $route->post('nestable_save', [SystemController::class, 'nestable_save'])->name('nestable_save');
});

/**
 * Admin Basic routes.
 */
Route::group([], function (Router $route) {

    $app_user_controller = admin_app_namespace('Controllers\\UserProfileController');
    $app_upload_controller = admin_app_namespace('Controllers\\UploadController');
    $app_dashboard_controller = admin_app_namespace('Controllers\\DashboardController');

    $route->get('profile', [class_exists($app_user_controller) ? $app_user_controller : UserController::class, 'index'])
        ->name('profile');
    $route->post('profile', [class_exists($app_user_controller) ? $app_user_controller : UserController::class, 'update'])
        ->name('profile.post');
    $route->get('profile/logout', [class_exists($app_user_controller) ? $app_user_controller : UserController::class, 'logout'])
        ->name('profile.logout');
    $route->post('uploader', [class_exists($app_upload_controller) ? $app_upload_controller : UploadController::class, 'index'])
        ->name('uploader');

    \Admin\Facades\NavigateFacade::item('admin.dashboard', 'dashboard')
        ->action([class_exists($app_dashboard_controller) ? $app_dashboard_controller : DashboardController::class, 'index'])
        ->icon_tachometer_alt();

    $route->namespace(admin_app_namespace('Controllers'))->group(static function (Router $route) {
        RoutesAdaptor::create_by_menu($route);
    });
});


