<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;

/**
 * Class Container
 * @package App\LteAdmin\Segments
 */
class Info extends Container {

    /**
     * Matrix constructor.
     * @param  \Closure|string  $title
     * @param  \Closure|null  $warp
     */
    public function __construct($title, \Closure $warp = null)
    {
        if ($title instanceof \Closure) {
            $warp = $title;
            $title = 'lte.information';
        }

        parent::__construct(function (DIV $div) use ($title, $warp) {
            $card = null;
            $div->card($title)->haveLink($card)
                ->defaultTools()
                ->foolBody()->model_info_table(function (ModelInfoTable $table) use ($warp, $card) {
                    if ($warp) {
                        ccc($warp, [
                            ModelInfoTable::class => $table,
                            Card::class => $card,
                            static::class => $this
                        ]);
                    }
                });
        });
    }
}