<?php

declare(strict_types=1);

namespace Admin\Widgets;

use Admin\Components\CardComponent;
use Admin\Components\WidgetComponent;
use Admin\Delegates\Card;
use Illuminate\Support\Arr;

class EnvironmentsWidget extends WidgetAbstract
{
    /**
     * The name of the widget.
     *
     * @var string|null
     */
    protected string|null $name = 'Environments Widget';

    /**
     * The description of the widget.
     *
     * @var string|null
     */
    protected string|null $description = 'This widget displays a environments statistic.';

    /**
     * The icon of the widget.
     *
     * @var string|null
     */
    protected string|null $icon = 'fab fa-envira';

    /**
     * Limiting the widget by roles.
     *
     * @var array|string[]
     */
    protected array $roles = [
        'root', 'admin'
    ];

    /**
     * Mandatory PHP extensions for the admin panel.
     *
     * @var array
     */
    protected array $requiredPhpExtensions = [
        'redis', 'xml', 'xmlreader', 'openssl', 'bcmath', 'json', 'mbstring', 'session', 'mysqlnd', 'PDO', 'pdo_mysql',
        'Phar', 'mysqli', 'SimpleXML', 'sockets', 'exif',
    ];

    /**
     * @param  \Admin\Components\WidgetComponent  $widgetComponent
     * @param  \Admin\Delegates\Card  $card
     * @return \Admin\Components\CardComponent|\Admin\Components\WidgetComponent|null
     */
    public function handle(WidgetComponent $widgetComponent, Card $card): CardComponent|WidgetComponent|null
    {
        return $widgetComponent->card(
            $card->title('admin.environment')
                ->fullBody()
                ->tableResponsive()
                ->table()
                ->rows([$this, 'environmentInfo'])
        );
    }

    /**
     * Display information about the environment.
     *
     * @return array
     */
    public function environmentInfo(): array
    {
        $mods = get_loaded_extensions();

        foreach ($mods as $key => $mod) {
            if (array_search($mod, $this->requiredPhpExtensions) !== false) {
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
}
