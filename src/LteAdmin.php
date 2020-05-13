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
    static $vesion = "2.0.0";

    /**
     * @var ExtendProvider[]
     */
    static $extensions = [];

    /**
     * @return \Lar\LteAdmin\Models\LteUser|\Illuminate\Contracts\Auth\Authenticatable|\App\Models\Admin
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

    /**
     * @param  ExtendProvider  $provider
     * @throws \Exception
     */
    public function registerExtension(ExtendProvider $provider)
    {
        if (!$provider::$name) {

            throw new \Exception("The extension name is not installed, set the extension name for the class (".get_class($provider).").");
        }

        if (!isset(LteAdmin::$extensions[$provider::$name])) {

            LteAdmin::$extensions[$provider::$name] = $provider;
        }
    }

    /**
     * @param  string  $name
     * @return bool|ExtendProvider
     */
    public function extension(string $name)
    {
        if (isset(LteAdmin::$extensions[$name])) {

            return LteAdmin::$extensions[$name];
        }

        return false;
    }

    /**
     * @return ExtendProvider[]
     */
    public function extensions()
    {
        return LteAdmin::$extensions;
    }
}
