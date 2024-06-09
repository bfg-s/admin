<?php

declare(strict_types=1);

namespace Admin\Widgets;

use Admin\Components\CardComponent;
use Admin\Components\WidgetComponent;
use Admin\Delegates\Card;
use Admin\Facades\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class LaravelInfoWidget extends WidgetAbstract
{
    /**
     * The name of the widget.
     *
     * @var string|null
     */
    protected string|null $name = 'Laravel Info Widget';

    /**
     * The description of the widget.
     *
     * @var string|null
     */
    protected string|null $description = 'This widget displays a Laravel info.';

    /**
     * The icon of the widget.
     *
     * @var string|null
     */
    protected string|null $icon = 'fab fa-laravel';

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
            $card->title('Laravel')
                ->fullBody()
                ->tableResponsive()
                ->table()
                ->rows([$this, 'laravelInfo'])
        );
    }

    /**
     * Display information about Laravel.
     *
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
}
