<?php

use LteAdmin\Controllers\AuthController;
use LteAdmin\Controllers\DashboardController;
use LteAdmin\Controllers\UploadController;
use LteAdmin\Controllers\UserController;
use LteAdmin\Models\LteUser;

return [

    /**
     * The dark mode by default for administrator
     */
    'dark_mode' => true,

    /**
     * Admin application namespace.
     */
    'app_namespace' => 'App\\Admin',

    /**
     * Package work dirs.
     */
    'paths' => [
        'app' => app_path('Admin'),
        'view' => 'admin',
    ],

    /**
     * Global rout configurations.
     */
    'route' => [
        'domain' => '',
        'prefix' => 'lte',
        'name' => 'lte.',
        'layout' => 'lte_layout',
    ],

    /**
     * Default actions.
     */
    'action' => [
        'auth' => [
            'login_form_action' => [AuthController::class, 'login'],
            'login_post_action' => [AuthController::class, 'login_post'],
        ],
        'profile' => [
            'index' => [UserController::class, 'index'],
            'update' => [UserController::class, 'update'],
            'logout' => [UserController::class, 'logout'],
        ],
        'dashboard' => [DashboardController::class, 'index'],
        'uploader' => [UploadController::class, 'index'],
    ],

    /**
     * Authentication settings for all lar admin pages. Include an authentication
     * guard and a user provider setting of authentication driver.
     */
    'auth' => [

        'guards' => [
            'lte' => [
                'driver' => 'session',
                'provider' => 'lte',
            ],
        ],

        'providers' => [
            'lte' => [
                'driver' => 'eloquent',
                'model' => LteUser::class,
            ],
        ],
    ],

    /**
     * Admin lte upload setting.
     *
     * File system configuration for form upload files and images, including
     * disk and upload path.
     */
    'upload' => [

        'disk' => 'lte',

        /**
         * Image and file upload path under the disk above.
         */
        'directory' => [
            'image' => 'images',
            'file' => 'files',
        ],
    ],

    /**
     * Admin lte use disks.
     */
    'disks' => [
        'lte' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads',
        ],
    ],

    'connections' => [
        'lte-sqlite' => [
            'driver' => 'sqlite',
            'url' => null,
            'database' => database_path('lte-database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ],

    'footer' => [
        'copy' => '<strong>Copyright &copy; '.date('Y').'.</strong> All rights reserved.',
    ],

    'lang_flags' => [
        'uk' => 'flag-icon flag-icon-ua',
        'en' => 'flag-icon flag-icon-us',
        'ru' => 'flag-icon flag-icon-ru',
    ],
];
