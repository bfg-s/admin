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
    static $vesion = "2.4.0";

    /**
     * @var ExtendProvider[]
     */
    static $nav_extensions = [];

    /**
     * @var ExtendProvider[]
     */
    static $installed_extensions = [];

    /**
     * @var ExtendProvider[]
     */
    static $not_installed_extensions = [];

    /**
     * @var bool[]
     */
    static $extensions;

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

            return false;
        }

        if (!LteAdmin::$extensions) {

            LteAdmin::$extensions = include storage_path('lte_extensions.php');
        }

        if (isset(LteAdmin::$extensions[$provider::$name])) {

            if (!isset(LteAdmin::$installed_extensions[$provider::$name])) {

                LteAdmin::$installed_extensions[$provider::$name] = $provider;

                if ($provider->included()) {

                    LteAdmin::$nav_extensions[$provider::$slug] = $provider;
                }
            }
        }

        else if (!isset(LteAdmin::$not_installed_extensions[$provider::$name])) {

            LteAdmin::$not_installed_extensions[$provider::$name] = $provider;
        }

        return true;
    }

    /**
     * @param  string  $name
     * @return bool|ExtendProvider
     */
    public function extension(string $name)
    {
        if (isset(LteAdmin::$installed_extensions[$name])) {

            return LteAdmin::$installed_extensions[$name];
        }

        return false;
    }

    /**
     * @return ExtendProvider[]
     */
    public function extensions()
    {
        return LteAdmin::$installed_extensions;
    }
}
