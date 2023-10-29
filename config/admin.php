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
    'languages' => ['en', 'ua', 'ru'],

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
     * Use force 2fa for all admin users
     */
    'force-2fa' => false,

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
     * Footer data
     */
    'footer' => [
        'copy' => 'Developed by <a href="https://swipex.ua/" target="_blank">Swipex</a>.
                    All rights reserved. <strong>Copyright &copy; 2020 - '.date('Y').'.</strong>',
    ],
];
