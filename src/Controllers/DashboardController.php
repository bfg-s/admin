<?php

namespace Admin\Controllers;

use App;
use Arr;
use Composer\Composer;
use DB;
use Lar\Layout\Abstracts\Component;
use Admin;
use Admin\Components\GridColumnComponent;
use Admin\Delegates\Card;
use Admin\Delegates\CardBody;
use Admin\Delegates\ChartJs;
use Admin\Delegates\SearchForm;
use Admin\Delegates\StatisticPeriod;
use Admin\Page;
use PDO;

class DashboardController extends Controller
{
    /**
     * @var array
     */
    protected $required = [
        'redis', 'xml', 'xmlreader', 'openssl', 'bcmath', 'json', 'mbstring', 'session', 'mysqlnd', 'PDO', 'pdo_mysql',
        'Phar', 'mysqli', 'SimpleXML', 'sockets', 'exif',
    ];

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  CardBody  $cardBody
     * @param  StatisticPeriod  $statisticPeriod
     * @param  ChartJs  $chartJs
     * @param  SearchForm  $searchForm
     * @return Page|mixed
     */
    public function index(
        Page $page,
        Card $card,
        CardBody $cardBody,
        StatisticPeriod $statisticPeriod,
        ChartJs $chartJs,
        SearchForm $searchForm
    ) {
        return $page->card(
            $card->title(__('admin.user_statistics')),
            $card->card_body(
                $cardBody->statistic_period(
                    $statisticPeriod->model(config('auth.providers.users.model'))
                        ->title('admin.users')
                        ->icon_users()
                        ->forToday()
                        ->perWeek()
                        ->perYear()
                        ->total()
                ),
                $cardBody->chart_js(
                    $chartJs->model(config('auth.providers.users.model'))
                        ->hasSearch(
                            $searchForm->date_range('created_at', 'admin.created_at')
                                ->default(implode(' - ', $this->defaultDateRange()))
                        )
                        ->setDefaultDataBetween('created_at', ...$this->defaultDateRange())
                        ->groupDataByAt('created_at')
                        ->eachPointCount(__('admin.added_to_users'))
                        ->miniChart(),
                )
            )
        )
            ->when(admin()->isRoot(), function (Page $page) {
                $page->next()->row(
                    $this->cardTableCol('admin.environment', [$this, 'environmentInfo']),
                    $this->cardTableCol('Laravel', [$this, 'laravelInfo']),
                    $this->cardTableCol('Composer', [$this, 'composerInfo']),
                    $this->cardTableCol('admin.database', [$this, 'databaseInfo']),
                );
            });
    }

    /**
     * @param  string  $title
     * @param $rows
     * @return GridColumnComponent
     */
    public function cardTableCol(string $title, $rows)
    {
        return $this->row->pl2()->column(
            $this->column->num(6)
                ->mb4()
                ->card()
                ->title($title)
                ->h100()
                ->full_body(['table-responsive'])
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
            __('admin.php_version') => '<span class="badge badge-dark">v'.versionString(PHP_VERSION).'</span>',
            __('admin.php_modules') => implode(', ', $mods),
            __('admin.cgi') => php_sapi_name(),
            __('admin.os') => php_uname(),
            __('admin.server') => Arr::get($_SERVER, 'SERVER_SOFTWARE'),
            //__('admin.root') => Arr::get($_SERVER, 'DOCUMENT_ROOT'),
            'System Load Average' => function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 0,
        ];
    }

    /**
     * @return array
     */
    public function laravelInfo()
    {
        $user_model = config('auth.providers.users.model');
        $admin_user_model = config('admin.auth.providers.admin.model');

        return [
            __('admin.laravel_version') => '<span class="badge badge-dark">v'.versionString(App::version()).'</span>',
            __('admin.admin_version') => '<span class="badge badge-dark">v'.versionString(Admin::version()).'</span>',
            __('admin.timezone') => config('app.timezone'),
            __('admin.language') => config('app.locale'),
            __('admin.languages_involved') => implode(', ', config('layout.languages')),
            __('admin.env') => config('app.env'),
            __('admin.url') => config('app.url'),
            __('admin.users') => number_format($user_model::count(), 0, '', ','),
            __('admin.admin_users') => number_format($admin_user_model::count(), 0, '', ','),
            '' => function (Component $component) {
                $component->_addClass(['table-secondary']);
                $component->_find('th')->h6(['m-0'], __('admin.drivers'));
            },
            __('admin.broadcast_driver') => '<span class="badge badge-secondary">'.config('broadcasting.default').'</span>',
            __('admin.cache_driver') => '<span class="badge badge-secondary">'.config('cache.default').'</span>',
            __('admin.session_driver') => '<span class="badge badge-secondary">'.config('session.driver').'</span>',
            __('admin.queue_driver') => '<span class="badge badge-secondary">'.config('queue.default').'</span>',
            __('admin.mail_driver') => '<span class="badge badge-secondary">'.config('mail.driver').'</span>',
            __('admin.hashing_driver') => '<span class="badge badge-secondary">'.config('hashing.driver').'</span>',
            __('admin.hashing_driver') => '<span class="badge badge-secondary">'.config('hashing.driver').'</span>',
        ];
    }

    /**
     * @return array
     */
    public function composerInfo()
    {
        $return = [
            __('admin.composer_version') => '<span class="badge badge-dark">v'.versionString(Composer::getVersion()).'</span>',
            '' => static function (Component $component) {
                $component->_addClass(['table-secondary']);
                $component->_find('th')->h6(['m-0'], __('admin.required'));
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
        /** @var PDO $pdo */
        $pdo = DB::query('SHOW VARIABLES')->getConnection()->getPdo();

        return [
            __('admin.server_version') => '<span class="badge badge-dark">v'.versionString($pdo->getAttribute(PDO::ATTR_SERVER_VERSION)).'</span>',
            __('admin.client_version') => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
            __('admin.server_info') => $pdo->getAttribute(PDO::ATTR_SERVER_INFO),
            __('admin.connection_status') => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS),
            __('admin.mysql_driver') => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
            '' => static function (Component $component) {
                $component->_addClass(['table-secondary']);
                $component->_find('th')->h6(['m-0'], __('admin.connection_info'));
            },
            __('admin.db_driver') => config('database.default'),
            __('admin.database') => env('DB_DATABASE'),
            __('admin.user') => env('DB_USERNAME'),
            __('admin.password') => str_repeat('*', strlen(env('DB_PASSWORD'))),
        ];
    }
}
