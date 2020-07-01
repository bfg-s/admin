<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\ModelTable;

/**
 * Class Container
 * @package App\LteAdmin\Segments
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
            $card = null;
            $div->card($title)->haveLink($card)
                ->defaultTools()
                ->bodyModelTable(function (ModelTable $table) use ($warp, $card) {
                    if ($warp) {
                        $warp($table, $card, $this);
                    }
                });
        });
    }
}