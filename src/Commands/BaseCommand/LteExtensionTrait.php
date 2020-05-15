<?php

namespace Lar\LteAdmin\Commands\BaseCommand;

use Lar\LteAdmin\LteAdmin;

/**
 * Trait LteExtensionTrait
 * @package Lar\LteAdmin\Commands\BaseCommand
 */
trait LteExtensionTrait {

    static $desc;

    /**
     * @param $name
     * @return bool
     */
    protected function any_exists_extension($name)
    {
        if (isset(LteAdmin::$installed_extensions[$name]) || isset(LteAdmin::$not_installed_extensions[$name])) {

            return true;
        }

        if (is_dir(lte_app_path("Extensions/{$name}"))) {

            return true;
        }

        return array_search($name, $this->getRemotes()) !== false;
    }

    /**
     * @param $name
     * @return array|bool
     */
    protected function validate_new_extension_name($name)
    {
        $name_parts = explode("/", $name);

        $name_parts = array_diff([null,'','lte','lte-admin'], $name_parts);

        return count($name_parts) !== 2;
    }

    /**
     * Enter description
     */
    protected function enterDescription()
    {
        if (!static::$desc) {

            while (!static::$desc) {

                static::$desc = $this->ask("Enter description of extension");
            }
        }
    }

    /**
     * @param  string  $file
     * @return false|string
     */
    protected function get_stub(string $file)
    {
        $data = file_get_contents(__DIR__ . "/Stubs/{$file}.stub");

        $name = $this->argument('name');

        list($folder, $extension) = explode("/", $name);

        $namespace = "Lar\\LteAdmin\\".ucfirst(\Str::camel($folder !== 'lar' ? $folder : 'extend'))."\\".ucfirst(\Str::camel($extension));

        $data = str_replace([
            '{NAME}', '{DESCRIPTION}', '{FOLDER}', '{EXTENSION}', '{LTE_VERSION}',
            '{COMPOSER_NAMESPACE}', '{NAMESPACE}'
        ], [
            $name, static::$desc, $folder, $extension, \LteAdmin::version(),
            str_replace('\\', '\\\\', $namespace), $namespace
        ], $data);

        return $data;
    }
}