<?php

namespace Lar\LteAdmin\Controllers;

use Composer\Composer;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\H4;
use Lar\LteAdmin\Segments\Container;
use Lar\LteAdmin\Segments\Tagable\Row;
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
     * @return Container
     */
    public function index()
    {
        return Container::create(function (DIV $div, Container $container) {

            $container->title('Test')
                ->icon_cogs()
                ->breadcrumb('Test', 'Test2');

            $prepend = config('lte.paths.view') . '.dashboard';

            if (\View::exists($prepend)) {

                $div->view($prepend);
            }

            $div->row(function (Row $row) {
                $row->col(6)->mb4()
                    ->card('lte.environment')->h100()
                    ->foolBody()->table($this->environmentInfo());

                $row->col(6)->mb4()
                    ->card('Laravel')->h100()
                    ->foolBody()->table($this->laravelInfo());

                $row->col(6)->mb4()
                    ->card('Composer')->h100()
                    ->foolBody()->table($this->composerInfo());

                $row->col(6)->mb4()
                    ->card('lte.database')->h100()
                    ->foolBody()->table($this->databaseInfo());
            });
        });
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
            __('lte.php_version') =>  "<span class=\"badge badge-dark\">v".versionString(PHP_VERSION)."</span>",
            __('lte.php_modules') => implode(', ', $mods),
            __('lte.cgi') => php_sapi_name(),
            __('lte.os') => php_uname(),
            __('lte.server') => \Arr::get($_SERVER, 'SERVER_SOFTWARE'),
            __('lte.root') => \Arr::get($_SERVER, 'DOCUMENT_ROOT'),
            'System Load Average' => sys_getloadavg()[0]
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
            __('lte.laravel_version') =>  "<span class=\"badge badge-dark\">v".versionString(\App::version())."</span>",
            __('lte.lte_version') =>   "<span class=\"badge badge-dark\">v".versionString(\LteAdmin::version())."</span>",
            __('lte.timezone') => config('app.timezone'),
            __('lte.language') => config('app.locale'),
            __('lte.languages_involved') => implode(', ', config('layout.languages')),
            __('lte.env') => config('app.env'),
            __('lte.url') => config('app.url'),
            __('lte.users') => number_format($user_model::count(), 0, '', ','),
            __('lte.lte_users') => number_format($lte_user_model::count(), 0, '', ','),
            '' => function (Component $component) {
                $component->_addClass(['table-secondary']);
                $component->_find('th')->h6(['m-0'], __('lte.drivers'));
            },
            __('lte.broadcast_driver') => "<span class=\"badge badge-secondary\">".config('broadcasting.default')."</span>",
            __('lte.cache_driver') => "<span class=\"badge badge-secondary\">".config('cache.default')."</span>",
            __('lte.session_driver') => "<span class=\"badge badge-secondary\">".config('session.driver')."</span>",
            __('lte.queue_driver') => "<span class=\"badge badge-secondary\">".config('queue.default')."</span>",
            __('lte.mail_driver') => "<span class=\"badge badge-secondary\">".config('mail.driver')."</span>",
            __('lte.hashing_driver') => "<span class=\"badge badge-secondary\">".config('hashing.driver')."</span>",
            __('lte.hashing_driver') => "<span class=\"badge badge-secondary\">".config('hashing.driver')."</span>",
        ];
    }

    /**
     * @return array
     */
    protected function composerInfo()
    {
        $return = [
            __('lte.composer_version') =>  "<span class=\"badge badge-dark\">v".versionString(Composer::getVersion())."</span>",
            '' => function (Component $component) {
                $component->_addClass(['table-secondary']);
                $component->_find('th')->h6(['m-0'], __('lte.required'));
            },
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
            __('lte.server_version') => "<span class=\"badge badge-dark\">v".versionString($pdo->getAttribute(\PDO::ATTR_SERVER_VERSION))."</span>",
            __('lte.client_version') => $pdo->getAttribute(\PDO::ATTR_CLIENT_VERSION),
            __('lte.server_info') => $pdo->getAttribute(\PDO::ATTR_SERVER_INFO),
            __('lte.connection_status') => $pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS),
            __('lte.mysql_driver') => $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME),
            '' => function (Component $component) {
                $component->_addClass(['table-secondary']);
                $component->_find('th')->h6(['m-0'], __('lte.connection_info'));
            },
            __('lte.db_driver') => config('database.default'),
            __('lte.database') => env('DB_DATABASE'),
            __('lte.user') => env('DB_USERNAME'),
            __('lte.password') => str_repeat('*', strlen(env('DB_PASSWORD'))),
        ];
    }
}
