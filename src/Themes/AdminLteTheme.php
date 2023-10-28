<?php

namespace Admin\Themes;

class AdminLteTheme extends Theme
{
    /**
     * @var string
     */
    protected string $name = 'AdminLte 3';

    /**
     * @var string
     */
    protected string $description = 'AdminLTE is an HTML template that can be used for any purpose.';

    /**
     * @var string
     */
    protected string $viewVariable = 'admin-lte::';

    /**
     * @var string|null
     */
    protected ?string $namespace = 'admin-lte';

    /**
     * @var string|null
     */
    protected ?string $directory = __DIR__ . '/../../lte';

    /**
     * @var string
     */
    protected string $slug = 'admin-lte';

    /**
     * @var array|string[]
     */
    protected array $styles = [
        'admin-asset/css/adminlte.min.css',
        'admin/css/dark.css',
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700',
    ];

    /**
     * @var array|string[]
     */
    protected array $scripts = [
        'admin-asset/js/adminlte.min.js',
    ];
}
