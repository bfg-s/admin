<?php

use Lar\Roads\Roads;

/**
 * Lte Auth routes
 */
Road::layout('lte_auth_layout')->namespace('Lar\LteAdmin\Controllers')->group(function (Roads $roads) {

    $roads->get('/', 'AuthController@login')->name('home');
    $roads->get('login', 'AuthController@login')->name('login');
    $roads->post('login', 'AuthController@login_post')->name('login.post');
});

/**
 * Basic routes
 */
Road::layout(config('lte.route.layout'))->group(function (Roads $roads) {

    $roads->namespace('Lar\LteAdmin\Controllers')->group(function (Roads $roads) {

        $roads->get('dashboard', 'DashboardController@index')->name('dashboard');
        $roads->get('profile', 'UserController@profile')->name('profile');
        $roads->post('profile', 'UserController@save')->name('profile.post');
        $roads->get('profile/logout', 'UserController@logout')->name('profile.logout');
        $roads->post('uploader', 'UploadController@index')->name('uploader');
        $roads->resource('lte_user', 'AdminsController');
    });

    $roads->namespace(config('lte.route.namespace'))->group(function (Roads $roads) {

        Navigate::item('Dashboard', 'dashboard')->icon_tachometer_alt();
        Navigate::item('Administrators', 'administrators')
            ->resource('lte_user', 'AdminsController', ['namespace' => '\Lar\LteAdmin\Controllers'])
            ->model(\Lar\LteAdmin\Models\LteUser::class)
            ->role('root')
            ->icon_users_cog()->ignored();

        \Lar\LteAdmin\Core\RoutesAdaptor::create_by_menu($roads);
    });
});
