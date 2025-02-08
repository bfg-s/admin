<?php

declare(strict_types=1);

namespace Admin\Themes;

/**
 * Class of the main theme of the admin panel.
 */
class FlowbiteAdminTheme extends Theme
{
    /**
     * Name of the visual theme.
     *
     * @var string
     */
    protected string $name = 'Flowbite 1.3';

    /**
     * Description of the visual theme.
     *
     * @var string
     */
    protected string $description = 'This project is a free and open-source UI admin dashboard template built with the components from Flowbite and based on the utility-first Tailwind CSS framework featuring charts, tables, widgets, CRUD layouts, modals, drawers, and more.';

    /**
     * Blade render variable to form the template path.
     *
     * @var string
     */
    protected string $viewVariable = 'admin-flowbite::';

    /**
     * Template namespace to form a template group.
     *
     * @var string|null
     */
    protected string|null $namespace = 'admin-flowbite';

    /**
     * Path to the templates of theme directory.
     *
     * @var string|null
     */
    protected string|null $directory = __DIR__.'/../../themes/flowbite';

    /**
     * Visual theme slug.
     *
     * @var string
     */
    protected string $slug = 'admin-flowbite';

    /**
     * Visual theme styles.
     *
     * @var array|string[]
     */
    protected array $styles = [
        'admin-asset/plugins/sweetalert2/sweetalert2.css',
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap',
        'admin/css/tailwind.css',
        'admin/flowbite-assets/app.css',
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
        'admin/flowbite-assets/app.bundle.js'
    ];
}
