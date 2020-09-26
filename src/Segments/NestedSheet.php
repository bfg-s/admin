<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Nested;

/**
 * Class NestedSheet
 * @package Lar\LteAdmin\Segments
 */
class NestedSheet extends Container {

    /**
     * NestedSheet constructor.
     * @param $title
     * @param  \Closure|array|null  $warp
     */
    public function __construct($title, $warp = null)
    {
        if (is_embedded_call($title)) {
            $warp = $title;
            $title = 'lte.list';
        }

        parent::__construct(function (DIV $div) use ($title, $warp) {
            $card = null;
            $div->card($title)->haveLink($card)
                ->nestedTools()
                ->defaultTools()
                ->body()
                ->nested(function (Nested $nested) use ($warp, $card) {
                    if (is_embedded_call($warp)) {
                        embedded_call($warp, [
                            Nested::class => $nested,
                            Card::class => $card,
                            static::class => $this
                        ]);
                    }
                });
        });
    }
}