<?php

declare(strict_types=1);

namespace Admin\Widgets;

use Admin\Components\CardComponent;
use Admin\Components\WidgetComponent;
use Admin\Delegates\Card;
use PDO;
use DB;

class DatabaseInfoWidget extends WidgetAbstract
{
    /**
     * The name of the widget.
     *
     * @var string|null
     */
    protected string|null $name = 'Database Info Widget';

    /**
     * The description of the widget.
     *
     * @var string|null
     */
    protected string|null $description = 'This widget displays a database info.';

    /**
     * The icon of the widget.
     *
     * @var string|null
     */
    protected string|null $icon = 'fas fa-database';

    /**
     * Limiting the widget by roles.
     *
     * @var array|string[]
     */
    protected array $roles = [
        'root', 'admin'
    ];

    /**
     * @param  \Admin\Components\WidgetComponent  $widgetComponent
     * @param  \Admin\Delegates\Card  $card
     * @return \Admin\Components\CardComponent|\Admin\Components\WidgetComponent|null
     */
    public function handle(WidgetComponent $widgetComponent, Card $card): CardComponent|WidgetComponent|null
    {
        return $widgetComponent->card(
            $card->title('admin.database')
                ->fullBody()
                ->tableResponsive()
                ->table()
                ->rows([$this, 'databaseInfo'])
        );
    }

    /**
     * Display information about the database.
     *
     * @return array
     */
    public function databaseInfo(): array
    {
        /** @var PDO $pdo */
        $pdo = DB::query('SHOW VARIABLES')->getConnection()->getPdo();

        return [
            __('admin.server_version') => '<span class="badge badge-dark">v'.admin_version_string($pdo->getAttribute(PDO::ATTR_SERVER_VERSION)).'</span>',
            __('admin.client_version') => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION).' ',
            __('admin.server_info') => $pdo->getAttribute(PDO::ATTR_SERVER_INFO).' ',
            __('admin.connection_status') => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS).' ',
            __('admin.mysql_driver') => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME).' ',
            __('admin.db_driver') => config('database.default'),
            __('admin.database') => env('DB_DATABASE'),
            __('admin.user') => env('DB_USERNAME').' ',
            __('admin.password') => str_repeat('*', strlen(env('DB_PASSWORD'))).' ',
        ];
    }
}
