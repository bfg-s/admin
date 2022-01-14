<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;

/**
 * Class Info
 * @package Lar\LteAdmin\Segments
 * @deprecated Use the LtePage
 */
class Info extends Container {

    /**
     * Matrix constructor.
     * @param  \Closure|string  $title
     * @param  \Closure|array|null  $warp
     */
    public function __construct($title, $warp = null)
    {
        if (is_embedded_call($title)) {
            $warp = $title;
            $title = 'lte.information';
        }

        parent::__construct(function (DIV $div) use ($title, $warp) {
            $card = null;
            $div->card($title)->haveLink($card)
                ->defaultTools()
                ->fullBody()->model_info_table(function (ModelInfoTable $table) use ($warp, $card) {
                    if (is_embedded_call($warp)) {
                        embedded_call($warp, [
                            ModelInfoTable::class => $table,
                            Card::class => $card,
                            static::class => $this
                        ]);
                    }
                });
        });
    }
}
