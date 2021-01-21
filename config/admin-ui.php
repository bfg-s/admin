<?php

return [

    /**
     * BFG Admin pane theme
     */
    'theme' => 'default',

    /**
     * Color schema of theme
     */
    'color' => 'light',

    /**
     * BFG Admin default plugins
     */
    'plugins' => [
        'styles' => [
            'theme/default/plugins/fontawesome/css/all.min.css',
        ],
        'scripts' => [

        ],
        'bscripts' => [
            'theme/default/plugins/popper.min.js',
            'theme/default/plugins/chart.js/chart.min.js',
            'theme/default/plugins/bootstrap/js/bootstrap.min.js',
        ],
    ],

    /**
     * A footer configurations
     */
    'footer' => [
        'copy' => '<strong>Copyright &copy; '.date('Y').'.</strong> All rights reserved.'
    ]
];
