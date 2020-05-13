<?php

return [

    'dashboard' => [
        'environment' => true,
        'composer' => true,
        'laravel' => true,
        'database' => true
    ],

    /**
     * Global rout configurations
     */
    'route' => [
        'domain' => '',
        'namespace' => 'App\LteAdmin\Controllers',
        'prefix' => 'lte',
        'name' => 'lte.',
        'layout' => 'lte_layout'
    ],

    /**
     * Default actions
     */
    'action' => [
        'auth' => [
            'login_form_action' => '\Lar\LteAdmin\Controllers\AuthController@login',
            'login_post_action' => '\Lar\LteAdmin\Controllers\AuthController@login_post'
        ],
        'profile' => [
            'index' => '\Lar\LteAdmin\Controllers\UserController@index',
            'update' => '\Lar\LteAdmin\Controllers\UserController@update',
            'logout' => '\Lar\LteAdmin\Controllers\UserController@logout'
        ],
        'dashboard' => '\Lar\LteAdmin\Controllers\DashboardController@index',
        'uploader' => '\Lar\LteAdmin\Controllers\UploadController@index',
        'lte_user' => '\Lar\LteAdmin\Controllers\AdminsController',
        'lte_role' => '\Lar\LteAdmin\Controllers\RolesController',
        'lte_permission' => '\Lar\LteAdmin\Controllers\PermissionController',
        'lte_functions' => '\Lar\LteAdmin\Controllers\FunctionsController',
    ],

    /**
     * Package work dirs
     */
    'paths' => [
        'app' => app_path('LteAdmin'),
        'view' => 'admin'
    ],

    /**
     * Authentication settings for all lar admin pages. Include an authentication
     * guard and a user provider setting of authentication driver.
     */
    'auth' => [

        'guards' => [
            'lte' => [
                'driver'   => 'session',
                'provider' => 'lte',
            ],
        ],

        'providers' => [
            'lte' => [
                'driver' => 'eloquent',
                'model'  => \Lar\LteAdmin\Models\LteUser::class,
            ],
        ],
    ],

    /**
     * Admin lte upload setting
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
            'file'  => 'files',
        ],
    ],

    /**
     * Admin lte use disks
     */
    'disks' => [
        'lte' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads',
        ]
    ],

    'footer' => [
        'copy' => '<strong>Copyright &copy; '.date('Y').'.</strong> All rights reserved.'
    ],

    'lang_flags' => [
        'uk' => 'flag-icon flag-icon-ua',
        'en' => 'flag-icon flag-icon-us',
        'ru' => 'flag-icon flag-icon-ru',
    ]
];
