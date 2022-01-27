<?php

use Lar\LteAdmin\Core\RoutesAdaptor;
use Lar\Roads\Roads;

/**
 * Lte Auth routes.
 */
Road::layout('lte_auth_layout')->group(static function (Roads $roads) {
    $roads->get('/', config('lte.action.auth.login_form_action'))->name('home');
    $roads->get('login', config('lte.action.auth.login_form_action'))->name('login');
    $roads->post('login', config('lte.action.auth.login_post_action'))->name('login.post');
});

/**
 * Basic routes.
 */
Road::layout(config('lte.route.layout'))->group(static function (Roads $roads) {
    $roads->get('profile', config('lte.action.profile.index'))->name('profile');
    $roads->post('profile', config('lte.action.profile.update'))->name('profile.post');
    $roads->get('profile/logout', config('lte.action.profile.logout'))->name('profile.logout');
    $roads->post('uploader', config('lte.action.uploader'))->name('uploader');

    $app_dashboard = lte_app_namespace('Controllers\\DashboardController');

    Navigate::item('lte.dashboard', 'dashboard')
        ->action(class_exists($app_dashboard) ? [$app_dashboard, 'index'] : config('lte.action.dashboard'))
        ->icon_tachometer_alt();

    $roads->namespace(lte_app_namespace('Controllers'))->group(static function (Roads $roads) {
        RoutesAdaptor::create_by_menu($roads);
    });
});

Route::emitter('lte');
