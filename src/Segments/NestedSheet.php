<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Nested;

/**
 * Class Container
 * @package App\LteAdmin\Segments
 */
class NestedSheet extends Container {

    /**
     * NestedSheet constructor.
     * @param $title
     * @param  \Closure|null  $warp
     */
    public function __construct($title, \Closure $warp = null)
    {
        if ($title instanceof \Closure) {
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
                    if ($warp) {
                        $warp($nested, $card, $this);
                    }
                });
        });
    }
}