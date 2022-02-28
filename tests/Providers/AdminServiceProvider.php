<?php

namespace LteAdmin\Tests\Providers;

use LteAdmin\ApplicationServiceProvider;
use LteAdmin\Tests\Admin\Config;
use LteAdmin\Tests\Admin\Navigator;

/**
 * AdminServiceProvider Class.
 * @package LteAdmin\Tests\Providers
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
