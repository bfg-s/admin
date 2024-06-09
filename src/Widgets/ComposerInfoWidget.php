<?php

declare(strict_types=1);

namespace Admin\Widgets;

use Admin\Components\CardComponent;
use Admin\Components\WidgetComponent;
use Admin\Delegates\Card;
use Composer\Composer;

class ComposerInfoWidget extends WidgetAbstract
{
    /**
     * The name of the widget.
     *
     * @var string|null
     */
    protected string|null $name = 'Composer Info Widget';

    /**
     * The description of the widget.
     *
     * @var string|null
     */
    protected string|null $description = 'This widget displays a Composer info.';

    /**
     * The icon of the widget.
     *
     * @var string|null
     */
    protected string|null $icon = 'fas fa-headphones';

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
            $card->title('Composer')
                ->fullBody()
                ->tableResponsive()
                ->table()
                ->rows([$this, 'composerInfo'])
        );
    }

    /**
     * Display information about the composer.
     *
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
}
