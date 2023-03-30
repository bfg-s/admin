<?php

namespace Admin\Tests\Providers;

use Admin\ApplicationServiceProvider;
use Admin\Tests\Admin\Config;
use Admin\Tests\Admin\Navigator;

/**
 * AdminServiceProvider Class.
 * @package Admin\Tests\Providers
 */
class AdminServiceProvider extends ApplicationServiceProvider
{
    /**
     * Protected variable Navigator.
     * @var string
     */
    protected $navigator = Navigator::class;

    /**
     * Protected variable Config.
     * @var string
     */
    protected $config = Config::class;
}
