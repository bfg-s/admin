<?php

namespace Admin\Controllers;

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
    ) {
        return $page->row(
            $row->column(8, $column->addClass('d-flex'))->card(
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
                            ->size(200)
                            ->load(function (Admin\Components\ChartJsComponent $component) {

                                $component->setDefaultDataBetween('created_at', ...$this->defaultDateRange())
                                    ->groupDataByAt('created_at')
                                    ->eachPointCount(__('admin.added_to_users'))
                                    ->miniChart();
                            }),
                    )
                )
            ),
            $row->column(4, $column->addClass('d-flex'))->row(
                $row->column(12, $column->addClass('d-flex'))->card(
                    $card->title(__('admin.administrators_browser_statistic')),
                    $card->card_body(
                        $cardBody->chart_js(
                            $chartJs->size(300)->typeDoughnut(),
                            $chartJs->load(function (Admin\Components\ChartJsComponent $component) {

                                $adminLogs = Admin\Models\AdminLog::all(['user_agent'])->map(
                                    fn (Admin\Models\AdminLog $log) => $this->getBrowser($log->user_agent)
                                )->groupBy('name')->map(
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

                                $adminLogs = Admin\Models\AdminLog::all(['title'])->map(
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
//            '' => function (Component $component) {
//                $component->_addClass(['table-secondary']);
//                $component->_find('th')->h6(['m-0'], __('admin.drivers'));
//            },
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
//            '' => static function (Component $component) {
//                $component->_addClass(['table-secondary']);
//                $component->_find('th')->h6(['m-0'], __('admin.required'));
//            },
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
            __('admin.client_version') => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION) . ' ',
            __('admin.server_info') => $pdo->getAttribute(PDO::ATTR_SERVER_INFO) . ' ',
            __('admin.connection_status') => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . ' ',
            __('admin.mysql_driver') => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . ' ',
//            '' => static function (Component $component) {
//                $component->_addClass(['table-secondary']);
//                $component->_find('th')->h6(['m-0'], __('admin.connection_info'));
//            },
            __('admin.db_driver') => config('database.default'),
            __('admin.database') => env('DB_DATABASE'),
            __('admin.user') => env('DB_USERNAME') . ' ',
            __('admin.password') => str_repeat('*', strlen(env('DB_PASSWORD'))) . ' ',
        ];
    }

    function getBrowser($u_agent) {
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }elseif(preg_match('/Firefox/i',$u_agent)){
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }elseif(preg_match('/OPR/i',$u_agent)){
            $bname = 'Opera';
            $ub = "Opera";
        }elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
            $bname = 'Apple Safari';
            $ub = "Safari";
        }elseif(preg_match('/Netscape/i',$u_agent)){
            $bname = 'Netscape';
            $ub = "Netscape";
        }elseif(preg_match('/Edge/i',$u_agent)){
            $bname = 'Edge';
            $ub = "Edge";
        }elseif(preg_match('/Trident/i',$u_agent)){
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }else {
                $version= $matches['version'][1];
            }
        }else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }
}
