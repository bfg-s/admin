<?php

namespace Admin;

use Admin\Models\AdminUser;
use Admin\Providers\AppServiceProvider;
use Admin\Providers\RunServiceProvider;

/**
 * Class Admin
 * @package Admin
 */
class Admin
{
    /**
     * @var string
     */
    protected $vesion = "1.0.3";

    /**
     * @return \Admin\Models\AdminUser|null
     */
    public function user()
    {
        /** @var AdminUser|null $user */
        $user = $this->guard()->user();
        return $user;
    }

    /**
     * @return bool
     */
    public function guest()
    {
        return $this->guard()->guest();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard()
    {
        return \Auth::guard('admin');
    }

    /**
     * @return string
     */
    public function version()
    {
        return $this->vesion;
    }

    /**
     * @return bool
     */
    public function installed()
    {
        return AppServiceProvider::$installed;
    }

    /**
     * Run bfg admin application
     * @return \Illuminate\Support\ServiceProvider
     */
    public function boot()
    {
        return app()->register(RunServiceProvider::class);
    }
}
