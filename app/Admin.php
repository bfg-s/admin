<?php

namespace Admin;

use Admin\Models\AdminUser;
use Admin\Providers\AppServiceProvider;

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
}
