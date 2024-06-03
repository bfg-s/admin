<?php

declare(strict_types=1);

namespace Admin\Themes;

/**
 * Class of the main theme of the admin panel.
 */
class AdminLteTheme extends Theme
{
    /**
     * Name of the visual theme.
     *
     * @var string
     */
    protected string $name = 'AdminLte 3';

    /**
     * Description of the visual theme.
     *
     * @var string
     */
    protected string $description = 'AdminLTE is an HTML template that can be used for any purpose.';

    /**
     * Blade render variable to form the template path.
     *
     * @var string
     */
    protected string $viewVariable = 'admin-lte::';

    /**
     * Template namespace to form a template group.
     *
     * @var string|null
     */
    protected string|null $namespace = 'admin-lte';

    /**
     * Path to the templates of theme directory.
     *
     * @var string|null
     */
    protected string|null $directory = __DIR__.'/../../lte';

    /**
     * Visual theme slug.
     *
     * @var string
     */
    protected string $slug = 'admin-lte';

    /**
     * Visual theme styles.
     *
     * @var array|string[]
     */
    protected array $styles = [
        'admin-asset/css/adminlte.min.css',
        'admin/css/dark.css',
        'admin-asset/plugins/sweetalert2/sweetalert2.css',
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700',
    ];

    /**
     * Initial visual theme scripts.
     *
     * @var array|string[]
     */
    protected array $firstScripts = [
        'admin-asset/plugins/jquery/jquery.min.js',
        'admin/plugins/jquery-ui.js',
        'admin-asset/plugins/bootstrap/js/bootstrap.bundle.min.js',
        'admin-asset/plugins/sweetalert2/sweetalert2.min.js',
    ];

    /**
     * Visual theme scripts.
     *
     * @var array|string[]
     */
    protected array $scripts = [
        'admin-asset/js/adminlte.min.js',
    ];
}
