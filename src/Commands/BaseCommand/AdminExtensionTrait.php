<?php

namespace Admin\Commands\BaseCommand;

use Composer\Json\JsonFormatter;
use Admin\Admin;
use Illuminate\Support\Str;

trait AdminExtensionTrait
{
    public static $desc;
    public static $author_name;
    public static $author_email;

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
            file_put_contents(
                base_path('composer.json'),
                JsonFormatter::format(json_encode($base_composer), false, true)
            );
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
            exec('cd '.base_path()." && composer {$command}");
        }

        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    protected function any_exists_extension($name)
    {
        if (isset(Admin::$installed_extensions[$name]) || isset(Admin::$not_installed_extensions[$name])) {
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
        $name_parts = explode('/', $name);

        $name_parts = array_diff($name_parts, [null, '', 'admin']);

        if (count($name_parts) !== 2) {
            return false;
        }

        if (is_dir(admin_app_path("Extensions/{$name}"))) {
            return false;
        }

        if (is_dir(base_path("vendor/{$name}"))) {
            return false;
        }

        return true;
    }

    /**
     * Enter description.
     */
    protected function enterDescription()
    {
        if (!static::$desc) {
            while (!static::$desc) {
                static::$desc = $this->ask('Enter description of extension');
            }
        }
        if (!static::$author_name) {
            while (!static::$author_name) {
                static::$author_name = $this->ask('Enter author name of extension');
            }
        }
        if (!static::$author_email) {
            while (!static::$author_email) {
                static::$author_email = $this->ask('Enter author email of extension');
            }
        }
    }

    /**
     * @param  string  $file
     * @return false|string
     */
    protected function get_stub(string $file)
    {
        $data = file_get_contents(__DIR__."/Stubs/{$file}.stub");

        $name = $this->argument('name');

        list($folder, $extension) = explode('/', $name);

        $namespace = 'Admin\\'.ucfirst(Str::camel($folder !== 'bfg' ? $folder : 'extend')).'\\'.ucfirst(Str::camel($extension));

        $data = str_replace([
            '{NAME}', '{DESCRIPTION}', '{FOLDER}', '{EXTENSION}', '{ADMIN_VERSION}',
            '{COMPOSER_NAMESPACE}', '{NAMESPACE}', '{SLUG}',
            '{AUTHOR_NAME}', '{AUTHOR_EMAIL}'
        ], [
            $name, static::$desc, $folder, $extension, \Admin::version(),
            str_replace('\\', '\\\\', $namespace), $namespace,
            Str::slug(str_replace('/', '_', $name), '_'),
            static::$author_name, static::$author_email
        ], $data);

        return $data;
    }
}
