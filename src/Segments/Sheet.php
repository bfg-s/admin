<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\ModelTable;

/**
 * Class Sheet
 * @package Lar\LteAdmin\Segments
 */
class Sheet extends Container {

    /**
     * Sheet constructor.
     * @param  \Closure|string  $title
     * @param  \Closure|null  $warp
     */
    public function __construct($title, \Closure $warp = null)
    {
        if ($title instanceof \Closure) {
            $warp = $title;
            $title = 'lte.list';
        }

        if (request()->has('show_deleted')) {
            $title = 'lte.deleted';
        }

        parent::__construct(function (DIV $div) use ($title, $warp) {
            /** @var Card $card */
            $card = null;
            $div->card($title)->haveLink($card)
                ->defaultTools()->bodyModelTable(function (ModelTable $table) use ($warp, $card) {
                    if ($warp) {
                        ccc($warp, [
                            ModelTable::class => $table,
                            Card::class => $card,
                            static::class => $this
                        ]);
                    }
                });
        });
    }
}