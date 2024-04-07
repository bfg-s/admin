<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Delegates\Column;
use Admin\Delegates\Row;
use App;
use Composer\Composer;
use DB;
use Illuminate\Support\Arr;
use Admin;
use Admin\Delegates\Card;
use Admin\Delegates\CardBody;
use Admin\Delegates\ChartJs;
use Admin\Delegates\SearchForm;
use Admin\Delegates\StatisticPeriod;
use Admin\Page;
use Illuminate\Support\Collection;
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
     * @param  Row  $row
     * @param  Column  $column
     * @return Page|mixed
     */
    public function index(
        Page $page,
        Card $card,
        CardBody $cardBody,
        StatisticPeriod $statisticPeriod,
        ChartJs $chartJs,
        SearchForm $searchForm,
        Admin\Delegates\Row $row,
        Admin\Delegates\Column $column,
    ): mixed {
        return $page->row(
            $row->column(12)->statistic_period(
                $statisticPeriod->model(config('auth.providers.users.model'))
                    ->title('admin.users')
                    ->icon_users()
                    ->forToday()
                    ->perWeek()
                    ->perYear()
                    ->total()
            ),
            $row->column(8)->row(
                //$row->addClass('w-100'),
                $row->column(12)->card(
                    $card->title(__('admin.user_statistics')),
                    $card->card_body(
                        $cardBody->chart_js(
                            $chartJs->model(config('auth.providers.users.model'))
                                ->hasSearch(
                                    $searchForm->date_range('created_at', 'admin.created_at')
                                        ->default(implode(' - ', $this->defaultDateRange()))
                                )
                                ->size(300)
                                ->load(function (Admin\Components\ChartJsComponent $component) {

                                    $component->setDefaultDataBetween('created_at', ...$this->defaultDateRange())
                                        ->groupDataByAt('created_at')
                                        ->eachPointCount(__('admin.added_to_users'))
                                        ->miniChart();
                                }),
                        )
                    )
                ),
            ),
            $row->column(4)->row(
                $row->addClass('h-100'),
                $row->column(12, $column->addClass('d-flex'))->card(
                    $card->title(__('admin.administrators_browser_statistic')),
                    $card->card_body(
                        $cardBody->chart_js(
                            $chartJs->size(300)->typeDoughnut(),
                            $chartJs->load(function (Admin\Components\ChartJsComponent $component) {

                                $adminLogs = Admin\Models\AdminBrowser::all(['name'])->groupBy('name')->map(
                                    fn (Collection $collection) => $collection->count()
                                );
                                $component->customChart(__('admin.browser'), [$adminLogs->toArray()], $adminLogs->map(
                                    fn () => $component->randColor()
                                )->values()->toArray());
                            }),
                        )
                    ),
                ),
                $row->column(12, $column->addClass('d-flex'))->card(
                    $card->title(__('admin.activity')),
                    $card->card_body(
                        $cardBody->chart_js(
                            $chartJs->size(300)->typeDoughnut(),
                            $chartJs->load(function (Admin\Components\ChartJsComponent $component) {

                                $adminLogs = admin()->logs()->where('title', '!=', 'Loaded page')->get(['title'])->map(
                                    fn (Admin\Models\AdminLog $log) => ['name' => $log->title]
                                )->groupBy('name')->map(
                                    fn (Collection $collection) => $collection->count()
                                );
                                $component->customChart(__('admin.menu_action'), [$adminLogs->toArray()], $adminLogs->map(
                                    fn () => $component->randColor()
                                )->values()->toArray());
                            }),
                        )
                    ),
                ),
            )
        )
            ->when(admin()->isRoot(), function (Page $page) {
                $page->view('controllers.dashboard-statistic', [
                    'environment' => $this->cardTableCol('admin.environment', [$this, 'environmentInfo']),
                    'laravel' => $this->cardTableCol('Laravel', [$this, 'laravelInfo']),
                    'composer' => $this->cardTableCol('Composer', [$this, 'composerInfo']),
                    'database' => $this->cardTableCol('admin.database', [$this, 'databaseInfo']),
                ]);
            });
    }

    /**
     * @param  string  $title
     * @param $rows
     * @return Admin\Components\CardComponent
     */
    public function cardTableCol(string $title, $rows)
    {
        $card = Admin\Components\CardComponent::create();

        $card->title($title)
            ->h100()
            ->full_body()
            ->tableResponsive()
            ->table()
            ->rows($rows);

        return $card;
    }

    /**
     * @return array
     */
    public function environmentInfo(): array
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
            __('admin.php_version') => '<span class="badge badge-dark">v'.admin_version_string(PHP_VERSION).'</span>',
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
    public function laravelInfo(): array
    {
        $user_model = config('auth.providers.users.model');
        $admin_user_model = config('admin.auth.providers.admin.model');

        return [
            __('admin.laravel_version') => '<span class="badge badge-dark">v'.admin_version_string(App::version()).'</span>',
            __('admin.admin_version') => '<span class="badge badge-dark">v'.admin_version_string(Admin::version()).'</span>',
            __('admin.timezone') => config('app.timezone'),
            __('admin.language') => config('app.locale'),
            __('admin.languages_involved') => implode(', ', config('admin.languages')),
            __('admin.env') => config('app.env'),
            __('admin.url') => config('app.url'),
            __('admin.users') => number_format($user_model::count(), 0, '', ','),
            __('admin.admin_users') => number_format($admin_user_model::count(), 0, '', ','),
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
    public function composerInfo(): array
    {
        $return = [
            __('admin.composer_version') => '<span class="badge badge-dark">v'.admin_version_string(Composer::getVersion()).'</span>',
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
    public function databaseInfo(): array
    {
        /** @var PDO $pdo */
        $pdo = DB::query('SHOW VARIABLES')->getConnection()->getPdo();

        return [
            __('admin.server_version') => '<span class="badge badge-dark">v'.admin_version_string($pdo->getAttribute(PDO::ATTR_SERVER_VERSION)).'</span>',
            __('admin.client_version') => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION) . ' ',
            __('admin.server_info') => $pdo->getAttribute(PDO::ATTR_SERVER_INFO) . ' ',
            __('admin.connection_status') => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . ' ',
            __('admin.mysql_driver') => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . ' ',
            __('admin.db_driver') => config('database.default'),
            __('admin.database') => env('DB_DATABASE'),
            __('admin.user') => env('DB_USERNAME') . ' ',
            __('admin.password') => str_repeat('*', strlen(env('DB_PASSWORD'))) . ' ',
        ];
    }
}
