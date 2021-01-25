<?php

namespace Admin;

use Admin\Extension\Extension;
use Admin\Models\AdminUser;

/**
 * Class AdminExtension
 * @package Admin
 */
class AdminExtension
{
    /**
     * @var Extension[]
     */
    protected $installed = [];

    /**
     * @var Extension[]
     */
    protected $not_installed = [];

    /**
     * @var bool[]
     */
    protected $extensions;

    /**
     * @param  \Admin\Extension\Extension  $provider
     * @throws \Exception
     */
    public function register(Extension $provider)
    {
        if (!$provider::$name) {

            return false;
        }

        if (!$this->extensions) {

            $this->extensions = include storage_path('admin_extensions.php');
        }

        if (isset($this->extensions[$provider::$name]) || $provider::$slug === 'application') {

            if (!isset($this->installed[$provider::$name])) {

                $this->installed[$provider::$name] = $provider;
            }
        }

        else if (!isset($this->not_installed[$provider::$name])) {

            $this->not_installed[$provider::$name] = $provider;
        }

        return true;
    }

    /**
     * @return \Admin\Extension\Extension[]
     */
    public function extensions()
    {
        return $this->installed;
    }

    /**
     * @return bool[]
     */
    public function list()
    {
        return $this->extensions;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function isIncluded(string $name)
    {
        return isset($this->extensions[$name]) ? $this->extensions[$name] : false;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function isProviderIncluded(string $name)
    {
        return $this->has($name) ? $this->get($name)->included() : false;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function has(string $name)
    {
        return isset($this->installed[$name]) || isset($this->not_installed[$name]);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function isInstalled(string $name)
    {
        return isset($this->installed[$name]);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function isNotInstalled(string $name)
    {
        return isset($this->not_installed[$name]);
    }

    /**
     * @param  string  $name
     * @return \Admin\Extension\Extension|null
     */
    public function get(string $name)
    {
        if (isset($this->installed[$name])) {
            return $this->installed[$name];
        } else if ($this->not_installed[$name]) {
            return $this->not_installed[$name];
        }
        return null;
    }

    /**
     * @return \Admin\Extension\Extension[]
     */
    public function notInstalled()
    {
        return $this->not_installed;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return array_merge(
            $this->installed,
            $this->not_installed
        );
    }
}
