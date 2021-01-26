<?php

namespace Admin\Extension\Providers;

use Admin\Extension\Extension;
use Illuminate\Support\Traits\Macroable;

/**
 * Class KernelProvider
 * @package Admin\Extension\Providers
 */
class KernelProvider {

    /**
     * @var Extension
     */
    public $provider;

    /**
     * Extension scripts
     * @var array
     */
    protected $scripts = [];

    /**
     * Extension body scripts
     * @var array
     */
    protected $bscripts = [];

    /**
     * Extension styles
     * @var array
     */
    protected $styles = [];

    /**
     * The event listener mappings for the admin application.
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
     * @var array
     */
    protected $subscribe = [];

    /**
     * KernelProvider constructor.
     * @param  Extension  $provider
     */
    public function __construct(Extension $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                \Event::listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            \Event::subscribe($subscriber);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Get extension scripts
     * @return array
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * Get extension body scripts
     * @return array
     */
    public function getBScripts()
    {
        return $this->bscripts;
    }

    /**
     * Get extension styles
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }
}