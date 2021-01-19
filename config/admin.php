<?php

return [

    /**
     * Admin application service provider
     */
    'provider' => \App\Providers\AdminServiceProvider::class,

    /**
     * Admin application namespace
     */
    'namespace' => "App\\Admin",

    /**
     * Package work dirs
     */
    'paths' => [
        'app' => app_path('Admin'),
        'view' => 'admin'
    ],

    /**
     * Global rout configurations
     */
    'route' => [
        'prefix' => 'admin',
        'name' => 'admin.',
        'layout' => \Admin\Layouts\DefaultAdminLayout::class,
        'namespace' => 'App\\Admin\\Controllers',
        'middlewares' => [
            'admin' => \Admin\Http\Middleware\Authenticate::class,
            'admin_layout' => \Admin\Http\Middleware\Layout::class
        ]
    ],

    /**
     * Default actions
     */
    'action' => [
        'auth' => [
            'login_form_action' => [\Admin\Http\Controllers\AuthController::class, 'login'],
            'login_post_action' => [\Admin\Http\Controllers\AuthController::class, 'login_post']
        ],
        'profile' => [
            'index' => [\Admin\Http\Controllers\UserController::class, 'index'],
            'update' => [\Admin\Http\Controllers\UserController::class, 'update'],
            'logout' => [\Admin\Http\Controllers\UserController::class, 'logout']
        ],
        'dashboard' => [\Admin\Http\Controllers\DashboardController::class, 'index'],
        'uploader' => [\Admin\Http\Controllers\UploadController::class, 'index'],
    ],

    /**
     * Authentication settings for all lar admin pages. Include an authentication
     * guard and a user provider setting of authentication driver.
     */
    'auth' => [

        'guards' => [
            'admin' => [
                'driver'   => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model'  => \Admin\Models\AdminUser::class,
            ],
        ],
    ],

    /**
     * Admin upload setting
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
            'file'  => 'files',
        ],
    ],

    /**
     * Admin use disks
     */
    'disks' => [
        'admin' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads',
        ]
    ],
];
