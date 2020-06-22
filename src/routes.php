<?php

use Lar\Roads\Roads;

/**
 * Lte Auth routes
 */
Road::layout('lte_auth_layout')->group(function (Roads $roads) {

    $roads->get('/', config('lte.action.auth.login_form_action'))->name('home');
    $roads->get('login', config('lte.action.auth.login_form_action'))->name('login');
    $roads->post('login', config('lte.action.auth.login_post_action'))->name('login.post');
});

/**
 * Basic routes
 */
Road::layout(config('lte.route.layout'))->group(function (Roads $roads) {

    $roads->get('profile', config('lte.action.profile.index'))->name('profile');
    $roads->post('profile', config('lte.action.profile.update'))->name('profile.post');
    $roads->get('profile/logout', config('lte.action.profile.logout'))->name('profile.logout');
    $roads->post('uploader', config('lte.action.uploader'))->name('uploader');

    Navigate::item('lte.dashboard', 'dashboard')
        ->action(config('lte.action.dashboard'))
        ->icon_tachometer_alt();

    Navigate::group('lte.administration', 'admin', function (\Lar\LteAdmin\Core\NavGroup $group) {

        $group->item('lte.administrators', 'administrators')
            ->resource('lte_user', config('lte.action.lte_user'))
            ->icon_users_cog();

        $group->group('lte.access', 'access', function (\Lar\LteAdmin\Core\NavGroup $group) {

            $group->item('lte.roles', 'roles')
                ->resource('lte_role', config('lte.action.lte_role'))
                ->model(\Lar\LteAdmin\Models\LteRole::class)
                ->icon_user_secret();

            $group->item('lte.permission', 'permission')
                ->resource('lte_permission', config('lte.action.lte_permission'))
                ->icon_ban();

            $group->item('lte.functions', 'functions')
                ->resource('lte_functions', config('lte.action.lte_functions'))
                ->model(\Lar\LteAdmin\Models\LteFunction::class)
                ->icon_dungeon()->role('root');

        })->icon_universal_access();

    })->icon_cogs();

    $roads->namespace(config('lte.route.namespace'))->group(function (Roads $roads) {

        \Lar\LteAdmin\Core\RoutesAdaptor::create_by_menu($roads);
    });
});
