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
            __('lte::admin.php_version') =>  "<span class=\"badge badge-dark\">v".versionString(PHP_VERSION)."</span>",
            __('lte::admin.php_modules') => implode(', ', $mods),
            __('lte::admin.cgi') => php_sapi_name(),
            __('lte::admin.os') => php_uname(),
            __('lte::admin.server') => \Arr::get($_SERVER, 'SERVER_SOFTWARE'),
            __('lte::admin.root') => \Arr::get($_SERVER, 'DOCUMENT_ROOT'),
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
            __('lte::admin.laravel_version') =>  "<span class=\"badge badge-dark\">v".versionString(\App::version())."</span>",
            __('lte::admin.lte_version') =>   "<span class=\"badge badge-dark\">v".versionString(\LteAdmin::version())."</span>",
            __('lte::admin.timezone') => config('app.timezone'),
            __('lte::admin.language') => config('app.locale'),
            __('lte::admin.languages_involved') => implode(', ', config('layout.languages')),
            __('lte::admin.env') => config('app.env'),
            __('lte::admin.url') => config('app.url'),
            __('lte::admin.users') => number_format($user_model::count(), 0, '', ','),
            __('lte::admin.lte_users') => number_format($lte_user_model::count(), 0, '', ','),

            [__('lte::admin.drivers')],
            __('lte::admin.cache_driver') => "<span class=\"badge badge-secondary\">".config('cache.default')."</span>",
            __('lte::admin.session_driver') => "<span class=\"badge badge-secondary\">".config('session.driver')."</span>",
            __('lte::admin.queue_driver') => "<span class=\"badge badge-secondary\">".config('queue.default')."</span>",
            __('lte::admin.mail_driver') => "<span class=\"badge badge-secondary\">".config('mail.driver')."</span>",
            __('lte::admin.hashing_driver') => "<span class=\"badge badge-secondary\">".config('hashing.driver')."</span>",
        ];
    }

    /**
     * @return array
     */
    protected function composerInfo()
    {
        $return = [
            __('lte::admin.composer_version') =>  "<span class=\"badge badge-dark\">v".versionString(Composer::getVersion())."</span>",
            [__('lte::admin.required')]
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
            __('lte::admin.server_version') => "<span class=\"badge badge-dark\">v".versionString($pdo->getAttribute(\PDO::ATTR_SERVER_VERSION))."</span>",
            __('lte::admin.client_version') => $pdo->getAttribute(\PDO::ATTR_CLIENT_VERSION),
            __('lte::admin.server_info') => $pdo->getAttribute(\PDO::ATTR_SERVER_INFO),
            __('lte::admin.connection_status') => $pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS),
            __('lte::admin.mysql_driver') => $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME),
            [__('lte::admin.connection_info')],
            __('lte::admin.db_driver') => config('database.default'),
            __('lte::admin.database') => env('DB_DATABASE'),
            __('lte::admin.user') => env('DB_USERNAME'),
            __('lte::admin.password') => str_repeat('*', strlen(env('DB_PASSWORD'))),
        ];
    }
}
