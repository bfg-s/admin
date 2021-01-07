<?php

namespace Admin\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ConsoleServiceProvider
 * @package Admin\Providers
 */
class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Console command classes
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}

