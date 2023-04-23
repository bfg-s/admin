<?php

use Lar\Roads\Roads;
use Admin\Controllers\SettingsController;
use Admin\Core\RoutesAdaptor;

/**
 * Admin Auth routes.
 */
\Lar\Roads\Facade::layout('admin_auth_layout')->group(static function (Roads $roads) {
    $roads->get('/', config('admin.action.auth.login_form_action'))->name('home');
    $roads->get('login', config('admin.action.auth.login_form_action'))->name('login');
    $roads->post('login', config('admin.action.auth.login_post_action'))->name('login.post');
});

/**
 * Admin Basic routes.
 */
\Lar\Roads\Facade::layout(config('admin.route.layout'))->group(static function (Roads $roads) {
    $roads->get('settings', [SettingsController::class, 'index'])->name('settings');
    $roads->get('profile', config('admin.action.profile.index'))->name('profile');
    $roads->post('profile', config('admin.action.profile.update'))->name('profile.post');
    $roads->get('profile/logout', config('admin.action.profile.logout'))->name('profile.logout');
    $roads->post('uploader', config('admin.action.uploader'))->name('uploader');

    $app_dashboard = admin_app_namespace('Controllers\\DashboardController');

    \Admin\Facades\NavigateFacade::item('admin.dashboard', 'dashboard')
        ->action(class_exists($app_dashboard) ? [$app_dashboard, 'index'] : config('admin.action.dashboard'))
        ->icon_tachometer_alt();

    $roads->namespace(admin_app_namespace('Controllers'))->group(static function (Roads $roads) {
        RoutesAdaptor::create_by_menu($roads);
    });
});
