<?php

use Admin\Controllers\DashboardController;
use Admin\Controllers\UploadController;
use Admin\Models\AdminUser;

return [

    /**
     * Admin secret panel key
     */
    'key' => env('ADMIN_KEY'),

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
    ],

    /**
     * Admin server list for monitoring.
     */
    'servers' => [
        [
            'name' => env('APP_NAME'),
            'host' => env('APP_URL'),
        ]
    ],

    /**
     * Default actions.
     */
    'action' => [
        'dashboard' => [DashboardController::class, 'index'],
        'uploader' => [UploadController::class, 'index'],
    ],

    /**
     * Default dashboard widgets.
     */
    'widgets' => [
        [
            fn () => \Admin\Widgets\PeriodStatisticWidget::create()->settings([
                'model' => config('auth.providers.users.model'),
                'title' => 'admin.users',
            ])->export(),
        ],
        [
            fn () => \Admin\Widgets\ChartStatisticWidget::create()->settings([
                'model' => config('auth.providers.users.model'),
                'title' => 'admin.user_statistics',
                'label' => 'admin.added_to_users',
            ])->export(),
        ],
        [
            fn () => \Admin\Widgets\PrivateNoteWidget::create()->export(),
        ],
        [
            fn () => \Admin\Widgets\AdministratorBrowserStatisticWidget::create()->export(),
            fn () => \Admin\Widgets\ActivityStatisticWidget::create()->export(),
        ],
        [
            fn () => \Admin\Widgets\EnvironmentsWidget::create()->export(),
            fn () => \Admin\Widgets\LaravelInfoWidget::create()->export(),
        ],
        [
            fn () => \Admin\Widgets\ComposerInfoWidget::create()->export(),
            fn () => \Admin\Widgets\DatabaseInfoWidget::create()->export(),
        ]
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
     * Fine-tuning the default model table component.
     */
    'model-table-component' => [
        'per_page' => 15,
        'per_pages' => [10, 15, 20, 50, 100, 500, 1000],
        'order_field' => 'id',
        'order_type' => 'desc',
    ],

    /**
     * Fine-tuning the default nested component.
     */
    'nested-component' => [
        'title_field' => 'name',
        'parent_field' => 'parent_id',
        'order_by_field' => 'order',
        'order_by_type' => 'asc',
        'max_depth' => 5,
    ],

    /**
     * Fine-tuning the default timeline component.
     */
    'timeline-component' => [
        'per_page' => 15,
        'per_pages' => [10, 15, 20, 50, 100],
        'icon_field' => 'icon',
        'title_field' => 'title',
        'order_field' => 'created_at',
        'order_type' => 'desc',
    ],

    /**
     * Fine-tuning the default model cards component.
     */
    'model-cards-component' => [
        'per_page' => 9,
        'per_pages' => [9, 15, 21, 51, 102, 501, 1002],
        'order_field' => 'id',
        'order_type' => 'desc',
    ],

    /**
     * Footer data
     */
    'footer' => [
        'copy' => 'Developed by <a href="https://github.com/bfg-s" target="_blank">Bfg</a>.
                    All rights reserved. <strong>Copyright &copy; 2020 - '.date('Y').'.</strong>',
    ],
];
