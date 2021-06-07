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
     * @param  bool  $tools
     */
    public function __construct($title, $warp = null, bool $tools = true)
    {
        if (is_bool($warp)) {
            $tools = $warp;
            $warp = null;
        }
        if (is_embedded_call($title)) {
            $warp = $title;
            $title = 'lte.list';
        }

        parent::__construct(function (DIV $div) use ($title, $warp, $tools) {
            $card = $div->card($title);

            if ($tools) {
                $card->nestedTools();
            }

            $card->defaultTools()
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
