<?php

namespace Lar\LteAdmin\Commands\BaseCommand;

use Composer\Json\JsonFormatter;
use Lar\LteAdmin\LteAdmin;

/**
 * Trait LteExtensionTrait
 * @package Lar\LteAdmin\Commands\BaseCommand
 */
trait LteExtensionTrait {

    static $desc;

    /**
     * @param  string  $path
     * @return bool
     */
    protected function add_repo_to_composer(string $path)
    {
        $base_composer = json_decode(file_get_contents(base_path('composer.json')), 1);

        if (!isset($base_composer['repositories'])) {
            $base_composer['repositories'] = [];
        }

        if (!collect($base_composer['repositories'])->where('url', $path)->first()) {
            $base_composer['repositories'][] = ['type' => 'path', 'url' => $path];
            file_put_contents(base_path('composer.json'), JsonFormatter::format(json_encode($base_composer), false, true));
            $this->info("> Add PATH [{$path}] to repository!");
            return true;
        }

        return false;
    }

    /**
     * @param  string  $command
     * @return null
     */
    protected function call_composer(string $command)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

            $this->comment("> Use \"composer {$command}\" for finish!");

        } else {

            exec('cd ' . base_path() . " && composer {$command}");
        }

        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    protected function any_exists_extension($name)
    {
        if (isset(LteAdmin::$installed_extensions[$name]) || isset(LteAdmin::$not_installed_extensions[$name])) {

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

        $name_parts = array_diff($name_parts, [null,'','lte']);

        if (count($name_parts) !== 2) {

            return false;
        }

        if (is_dir(lte_app_path("Extensions/{$name}"))) {

            return false;
        }

        if (is_dir(base_path("vendor/{$name}"))) {

            return false;
        }

        return true;
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
            '{COMPOSER_NAMESPACE}', '{NAMESPACE}', '{SLUG}'
        ], [
            $name, static::$desc, $folder, $extension, \LteAdmin::version(),
            str_replace('\\', '\\\\', $namespace), $namespace,
            \Str::slug(str_replace("/", "_", $name), '_')
        ], $data);

        return $data;
    }
}