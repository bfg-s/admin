<?php

namespace Admin\Extension\Providers;

use Admin\Extension\Extension;

/**
 * Class AppServiceProvider
 * @package Lar\LteAdmin
 */
class ApplicationProvider extends Extension
{
    /**
     * Extension call slug
     * @var string
     */
    static $slug = "application";

    /**
     * @throws \Exception
     */
    public function register()
    {
        if (!static::$name) {

            static::$name = static::$slug;
        }

        if (!static::$description) {

            static::$description = \config('app.name');
        }

        parent::register();
    }

    /**
     * @return bool
     */
    public function included()
    {
        return true;
    }
}

