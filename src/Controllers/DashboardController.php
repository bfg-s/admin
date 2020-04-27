<?php

namespace Lar\LteAdmin\Controllers;

use Composer\Composer;
use PDO;

/**
 * Class DashboardController
 *
 * @package Lar\LteAdmin\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @var array
     */
    protected $required = [
        'redis', 'xml', 'xmlreader', 'openssl', 'bcmath', 'json', 'mbstring', 'session', 'mysqlnd', 'PDO', 'pdo_mysql', 'Phar', 'mysqli', 'SimpleXML', 'sockets', 'exif'
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //dd(gets()->lte->menu->now_menu_parents);

        return view('lte::dashboard', [
            'environment' => $this->environmentInfo(),
            'laravel' => $this->laravelInfo(),
            'composer' => $this->composerInfo(),
            'database' => $this->databaseInfo()
        ]);
    }

    /**
     * @return array
     */
    protected function environmentInfo()
    {
        $mods = get_loaded_extensions();

        foreach ($mods as $key => $mod) {

            if (array_search($mod, $this->required) !== false) {

                $mods[$key] = "<span class=\"badge badge-success\">{$mod}</span>";
            }

            else {

                $mods[$key] = "<span class=\"badge badge-warning\">{$mod}</span>";
            }
        }

        return [
            __('lte::dashboard.php_version') =>  "<span class=\"badge badge-dark\">v".versionString(PHP_VERSION)."</span>",
            __('lte::dashboard.php_modules') => implode(', ', $mods),
            __('lte::dashboard.cgi') => php_sapi_name(),
            __('lte::dashboard.os') => php_uname(),
            __('lte::dashboard.server') => \Arr::get($_SERVER, 'SERVER_SOFTWARE'),
            __('lte::dashboard.root') => \Arr::get($_SERVER, 'DOCUMENT_ROOT'),
        ];
    }

    /**
     * @return array
     */
    protected function laravelInfo()
    {
        $user_model = config('auth.providers.users.model');
        $lte_user_model = config('lte.auth.providers.lte.model');

        return [
            __('lte::dashboard.laravel_version') =>  "<span class=\"badge badge-dark\">v".versionString(\App::version())."</span>",
            __('lte::dashboard.lte_version') =>   "<span class=\"badge badge-dark\">v".versionString(\LteAdmin::version())."</span>",
            __('lte::dashboard.timezone') => config('app.timezone'),
            __('lte::dashboard.language') => config('app.locale'),
            __('lte::dashboard.languages_involved') => implode(', ', config('layout.languages')),
            'Env' => config('app.env'),
            'URL' => config('app.url'),
            __('lte::dashboard.users') => number_format($user_model::count(), 0, '', ','),
            __('lte::dashboard.lte_users') => number_format($lte_user_model::count(), 0, '', ','),

            ['Drivers'],
            __('lte::dashboard.cache_driver') => "<span class=\"badge badge-secondary\">".config('cache.default')."</span>",
            __('lte::dashboard.session_driver') => "<span class=\"badge badge-secondary\">".config('session.driver')."</span>",
            __('lte::dashboard.queue_driver') => "<span class=\"badge badge-secondary\">".config('queue.default')."</span>",
            __('lte::dashboard.mail_driver') => "<span class=\"badge badge-secondary\">".config('mail.driver')."</span>",
            __('lte::dashboard.hashing_driver') => "<span class=\"badge badge-secondary\">".config('hashing.driver')."</span>",
        ];
    }

    /**
     * @return array
     */
    protected function composerInfo()
    {
        $return = [
            __('lte::dashboard.composer_version') =>  "<span class=\"badge badge-dark\">v".versionString(Composer::getVersion())."</span>",
            ['Required']
        ];

        $json = file_get_contents(base_path('composer.json'));

        $dependencies = json_decode($json, true)['require'];

        foreach ($dependencies as $name => $ver) {

            $return[$name] = "<span class=\"badge badge-dark\">{$ver}</span>";
        }

        return $return;
    }

    /**
     * @return array
     */
    protected function databaseInfo()
    {
        /** @var \PDO $pdo */
        $pdo = \DB::query("SHOW VARIABLES")->getConnection()->getPdo();

        return [
            'Server version' => "<span class=\"badge badge-dark\">v".versionString($pdo->getAttribute(PDO::ATTR_SERVER_VERSION))."</span>",
            'Client version' => $pdo->getAttribute(\PDO::ATTR_CLIENT_VERSION),
            'Server info' => $pdo->getAttribute(PDO::ATTR_SERVER_INFO),
            'Connection status' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS),
            'Driver name' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
            ['Connection info'],
            __('lte::dashboard.db_driver') => config('database.default'),
            'Database' => env('DB_DATABASE'),
            'User' => env('DB_USERNAME'),
            'Password' => str_repeat('*', strlen(env('DB_PASSWORD'))),
        ];
    }
}
