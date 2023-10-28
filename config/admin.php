<?php

use Admin\Controllers\AuthController;
use Admin\Controllers\DashboardController;
use Admin\Controllers\UploadController;
use Admin\Controllers\UserController;
use Admin\Models\AdminUser;

return [

    /**
     * The theme of admin panel
     */
    'theme' => 'admin-lte',

    /**
     * The dark mode by default for administrator
     */
    'dark_mode' => true,

    /**
     * Language mode for admin panel
     */
    'lang_mode' => true,

    /**
     * Supported languages
     */
    'languages' => ['en', 'uk', 'ru'],

    /**
     * Default home route
     */
    'home-route' => 'admin.dashboard',

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
        'prefix' => 'bfg',
        'name' => 'admin.',
        'layout' => 'admin_layout',
    ],

    /**
     * Default actions.
     */
    'action' => [
        'dashboard' => [DashboardController::class, 'index'],
        'uploader' => [UploadController::class, 'index'],
    ],

    /**
     * Additional repo functional
     */
    'functional' => [
        'menu' => false,
        'settings' => false,
        'force-2fa' => false,
    ],

    /**
     * Authentication settings for all admin pages. Include an authentication
     * guard and a user provider setting of authentication driver.
     */
    'auth' => [

        'guards' => [
            'admin' => [
                'driver' => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model' => AdminUser::class,
            ],
        ],
    ],

    /**
     * Admin upload setting.
     *
     * File system configuration for form upload files and images, including
     * disk and upload path.
     */
    'upload' => [

        'disk' => 'admin',

        /**
         * Image and file upload path under the disk above.
         */
        'directory' => [
            'image' => 'images',
            'file' => 'files',
        ],
    ],

    /**
     * Admin use disks.
     */
    'disks' => [
        'admin' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads',
        ],
    ],

    /**
     * SQLite's connection for menu and settings
     */
    'connections' => [
        'admin-sqlite' => [
            'driver' => 'sqlite',
            'url' => null,
            'database' => database_path('admin-database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ],

    /**
     * Footer data
     */
    'footer' => [
        'copy' => '<strong>Copyright &copy; '.date('Y').'.</strong> All rights reserved.',
    ],

    /**
     * Language flag icons
     */
    'lang_flags' => [
        'uk' => 'flag-icon flag-icon-ua',
        'en' => 'flag-icon flag-icon-us',
        'ru' => 'flag-icon flag-icon-ru',
    ],
];
