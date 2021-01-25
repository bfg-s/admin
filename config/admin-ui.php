<?php

return [
    /**
     * Admin panel logo url
     */
    'logo' => asset('vendor/admin/images/bfg-logo-big.png'),

    /**
     * Admin panel favicon url
     */
    'favicon' => admin_asset('images/bfg-logo.png'),

    /**
     * BFG Admin default plugins
     */
    'plugins' => [
        'styles' => [
            'vendor/admin/theme/default/plugins/fontawesome/css/all.min.css',
            'vendor/admin/theme/default/plugins/bootstrap-icons.css',
        ],
        'scripts' => [
        ],
        'bscripts' => [
            'vendor/admin/theme/default/plugins/popper.min.js',
            'vendor/admin/theme/default/plugins/chart.js/chart.min.js',
        ],
    ],

    /**
     * A footer configurations
     */
    'footer' => [
        'copy' => '<strong>Copyright &copy; '.date('Y').'.</strong> All rights reserved.'
    ]
];
