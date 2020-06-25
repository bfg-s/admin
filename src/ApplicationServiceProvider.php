<?php

namespace Lar\LteAdmin;

/**
 * Class AppServiceProvider
 * @package Lar\LteAdmin
 */
class ApplicationServiceProvider extends ExtendProvider
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
        static::$name = \config('app.name');

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

