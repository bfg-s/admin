<?php

namespace Lar\LteAdmin;

/**
 * Class LteAdmin
 *
 * @package Lar\CryptoApi
 */
class LteAdmin
{
    /**
     * @var string
     */
    protected static $vesion = "2.1.0";

    /**
     * @return \Lar\LteAdmin\Models\LteUser
     */
    public function user()
    {
        return \Auth::guard('lte')->user();
    }

    /**
     * @return mixed
     */
    public function guest()
    {
        return \Auth::guard('lte')->guest();
    }

    /**
     * @return string
     */
    public function version()
    {
        return LteAdmin::$vesion;
    }
}
