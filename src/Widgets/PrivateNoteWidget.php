<?php

declare(strict_types=1);

namespace Admin\Widgets;

use Admin\Components\CardComponent;
use Admin\Components\StatisticPeriodComponent;
use Admin\Components\WidgetComponent;
use Admin\Delegates\Card;
use Admin\Delegates\CardBody;
use Admin\Delegates\StatisticPeriod;
use Admin\Respond;
use Illuminate\Support\Facades\Cache;

class PrivateNoteWidget extends WidgetAbstract
{
    /**
     * The name of the widget.
     *
     * @var string|null
     */
    protected string|null $name = 'Private Note Widget';

    /**
     * The description of the widget.
     *
     * @var string|null
     */
    protected string|null $description = 'A widget that displays and manages private notes.';

    /**
     * The icon of the widget.
     *
     * @var string|null
     */
    protected string|null $icon = 'far fa-clipboard';

    /**
     * @param  \Admin\Components\WidgetComponent  $widgetComponent
     * @param  \Admin\Delegates\Card  $card
     * @param  \Admin\Delegates\CardBody  $cardBody
     * @return \Admin\Components\CardComponent|\Admin\Components\WidgetComponent|null
     */
    public function handle(WidgetComponent $widgetComponent, Card $card, CardBody $cardBody): CardComponent|WidgetComponent|null
    {
        return $widgetComponent->card(
            $card->title('admin.private_notes'),
            $card->card_body(
                $cardBody->textarea('private_note')
                    ->rows(10)
                    ->value(Cache::get('private_note_' . auth()->id(), ''))
                    ->fullHeight()
                    ->change(function (Respond $respond, $value = '') {
                        Cache::forever('private_note_' . auth()->id(), $value);
                        return $respond->toast_success('admin.private_note_saved_successfully');
                    })
            ),
        );
    }
}
