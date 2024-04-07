<?php

declare(strict_types=1);

namespace Admin\Themes;

class PublishedAdminLteTheme extends AdminLteTheme
{
    /**
     * @var string
     */
    protected string $name = 'Published AdminLte 3';

    /**
     * @var string
     */
    protected string $description = 'Published AdminLTE theme.';

    /**
     * @var string
     */
    protected string $viewVariable = 'vendor.admin.lte.';

    /**
     * @var string|null
     */
    protected ?string $namespace = null;

    /**
     * @var string|null
     */
    protected ?string $directory = null;

    /**
     * @var string
     */
    protected string $slug = 'published-admin-lte';
}
