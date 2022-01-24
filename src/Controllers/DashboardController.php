<?php

namespace Lar\LteAdmin\Controllers;

use Composer\Composer;
use Lar\Layout\Abstracts\Component;
use Lar\LteAdmin\Delegates\Card;
use Lar\LteAdmin\Delegates\CardBody;
use Lar\LteAdmin\Delegates\ChartJs;
use Lar\LteAdmin\Delegates\SearchForm;
use Lar\LteAdmin\Delegates\StatisticPeriod;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Page;

class DashboardController extends Controller
{
    /**
     * @var string
     */
    public static $model = LteUser::class;

    /**
     * @var array
     */
    protected $required = [
        'redis', 'xml', 'xmlreader', 'openssl', 'bcmath', 'json', 'mbstring', 'session', 'mysqlnd', 'PDO', 'pdo_mysql', 'Phar', 'mysqli', 'SimpleXML', 'sockets', 'exif',
    ];

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  CardBody  $cardBody
     * @param  ChartJs  $chartJs
     * @return Page|mixed
     */
    public function index(Page $page, Card $card, CardBody $cardBody, StatisticPeriod $statisticPeriod, ChartJs $chartJs, SearchForm $searchForm)
    {
        return $page->card(
            $card->title(__('lte.user_statistics')),
            $card->card_body(
                $cardBody->statistic_period(
                    $statisticPeriod->model(config('auth.providers.users.model'))
                        ->title('lte.users')
                        ->icon_users()
                        ->forToday()
                        ->perWeek()
                        ->perYear()
                        ->total()
                ),
                $cardBody->chart_js(
                    $chartJs->model(config('auth.providers.users.model'))
                        ->hasSearch(
                            $searchForm->date_range('created_at', 'lte.created_at')
                                ->default(implode(' - ', $this->defaultDateRange()))
                        )
                        ->setDefaultDataBetween('created_at', ...$this->defaultDateRange())
                        ->groupDataByAt('created_at')
                        ->eachPointCount(__('lte.added_to_users'))
                        ->miniChart(),
                )
            )
        )
        ->when(admin()->isRoot(), function (Page $page) {
            $page->next()->row(
                $this->cardTableCol('lte.environment', [$this, 'environmentInfo']),
                $this->cardTableCol('Laravel', [$this, 'laravelInfo']),
                $this->cardTableCol('Composer', [$this, 'composerInfo']),
                $this->cardTableCol('lte.database', [$this, 'databaseInfo']),
            );
        });
    }

    /**
     * @param  string  $title
     * @param $rows
     * @return \Lar\LteAdmin\Components\GridColumnComponent
     */
    public function cardTableCol(string $title, $rows)
    {
        return $this->row->pl2()->column(
            $this->column->num(6)
                ->mb4()
                ->card()
                ->title($title)
                ->h100()
                ->fullBody(['table-responsive'])
                ->table()
                ->rows($rows)
        );
    }

    /**
     * @return array
     */
    public function environmentInfo()
    {
        $mods = get_loaded_extensions();

        foreach ($mods as $key => $mod) {
            if (array_search($mod, $this->required) !== false) {
                $mods[$key] = "<span class=\"badge badge-success\">{$mod}</span>";
            } else {
                $mods[$key] = "<span class=\"badge badge-warning\">{$mod}</span>";
            }
        }

        return [
            __('lte.php_version') =>  '<span class="badge badge-dark">v'.versionString(PHP_VERSION).'</span>',
            __('lte.php_modules') => implode(', ', $mods),
            __('lte.cgi') => php_sapi_name(),
            __('lte.os') => php_uname(),
            __('lte.server') => \Arr::get($_SERVER, 'SERVER_SOFTWARE'),
            __('lte.root') => \Arr::get($_SERVER, 'DOCUMENT_ROOT'),
            'System Load Average' => function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 0,
        ];
    }

    /**
     * @return array
     */
    public function laravelInfo()
    {
        $user_model = config('auth.providers.users.model');
        $lte_user_model = config('lte.auth.providers.lte.model');

        return [
            __('lte.laravel_version') =>  '<span class="badge badge-dark">v'.versionString(\App::version()).'</span>',
            __('lte.lte_version') =>   '<span class="badge badge-dark">v'.versionString(\LteAdmin::version()).'</span>',
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
            __('lte.broadcast_driver') => '<span class="badge badge-secondary">'.config('broadcasting.default').'</span>',
            __('lte.cache_driver') => '<span class="badge badge-secondary">'.config('cache.default').'</span>',
            __('lte.session_driver') => '<span class="badge badge-secondary">'.config('session.driver').'</span>',
            __('lte.queue_driver') => '<span class="badge badge-secondary">'.config('queue.default').'</span>',
            __('lte.mail_driver') => '<span class="badge badge-secondary">'.config('mail.driver').'</span>',
            __('lte.hashing_driver') => '<span class="badge badge-secondary">'.config('hashing.driver').'</span>',
            __('lte.hashing_driver') => '<span class="badge badge-secondary">'.config('hashing.driver').'</span>',
        ];
    }

    /**
     * @return array
     */
    public function composerInfo()
    {
        $return = [
            __('lte.composer_version') =>  '<span class="badge badge-dark">v'.versionString(Composer::getVersion()).'</span>',
            '' => static function (Component $component) {
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
    public function databaseInfo()
    {
        /** @var \PDO $pdo */
        $pdo = \DB::query('SHOW VARIABLES')->getConnection()->getPdo();

        return [
            __('lte.server_version') => '<span class="badge badge-dark">v'.versionString($pdo->getAttribute(\PDO::ATTR_SERVER_VERSION)).'</span>',
            __('lte.client_version') => $pdo->getAttribute(\PDO::ATTR_CLIENT_VERSION),
            __('lte.server_info') => $pdo->getAttribute(\PDO::ATTR_SERVER_INFO),
            __('lte.connection_status') => $pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS),
            __('lte.mysql_driver') => $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME),
            '' => static function (Component $component) {
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
