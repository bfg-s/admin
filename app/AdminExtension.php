<?php

namespace Admin;

use Admin\Extension\Extension;
use Admin\Models\AdminUser;

/**
 * Class Admin
 * @package Bfg\Admin
 */
class AdminExtension
{
    /**
     * @var string
     */
    protected $vesion = "1.0.3";

    /**
     * @var Extension[]
     */
    protected $installed_extensions = [];

    /**
     * @var Extension[]
     */
    protected $not_installed_extensions = [];

    /**
     * @var bool[]
     */
    protected $extensions;

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
     * @param  \Admin\Extension\Extension  $provider
     * @throws \Exception
     */
    public function registerExtension(Extension $provider)
    {
        if (!$provider::$name) {

            return false;
        }

        if (!$this->extensions) {

            $this->extensions = include storage_path('admin_extensions.php');
        }

        if (isset($this->extensions[$provider::$name]) || $provider::$slug === 'application') {

            if (!isset($this->installed_extensions[$provider::$name])) {

                $this->installed_extensions[$provider::$name] = $provider;
            }
        }

        else if (!isset($this->not_installed_extensions[$provider::$name])) {

            $this->not_installed_extensions[$provider::$name] = $provider;
        }

        return true;
    }

    /**
     * @param  string  $name
     * @return bool|\Admin\Extension\Extension
     */
    public function extension(string $name)
    {
        if (isset($this->installed_extensions[$name])) {

            return $this->installed_extensions[$name];
        }

        return false;
    }

    /**
     * @return \Admin\Extension\Extension[]
     */
    public function extensions()
    {
        return $this->installed_extensions;
    }

    /**
     * @return bool[]
     */
    public function extensionList()
    {
        return $this->extensions;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function isIncludedExtension(string $name)
    {
        return $this->hasExtension($name) ? $this->extensions[$name] : false;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function hasExtension(string $name)
    {
        return isset($this->installed_extensions[$name]) || isset($this->not_installed_extensions[$name]);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function hasInInstalledExtension(string $name)
    {
        return isset($this->installed_extensions[$name]);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function hasInNotInstalledExtension(string $name)
    {
        return isset($this->not_installed_extensions[$name]);
    }

    /**
     * @param  string  $name
     * @return \Admin\Extension\Extension|null
     */
    public function getExtension(string $name)
    {
        if (isset($this->installed_extensions[$name])) {
            return $this->installed_extensions[$name];
        } else if ($this->not_installed_extensions[$name]) {
            return $this->not_installed_extensions[$name];
        }
        return null;
    }

    /**
     * @return \Admin\Extension\Extension[]
     */
    public function notInstalledExtensions()
    {
        return $this->not_installed_extensions;
    }

    /**
     * @return array
     */
    public function getAllExtension()
    {
        return array_merge(
            $this->installed_extensions,
            $this->not_installed_extensions
        );
    }

    /**
     * @return string[]
     */
    public function extensionProviders()
    {
        return array_flip(
            array_map(
                'get_class',
                $this->getAllExtension()
            )
        );
    }

    /**
     * Boot of admin application
     */
    public function boot()
    {
        foreach ($this->extensions() as $extension) {

            if ($extension->included()) {

                $extension->config()->boot();
            }
        }
    }

    /**
     * @return string
     */
    public function version()
    {
        return $this->vesion;
    }
}
