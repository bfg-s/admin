<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;

/**
 * Class Matrix
 * @package Lar\LteAdmin\Segments
 */
class Matrix extends Container {

    /**
     * Matrix constructor.
     * @param  \Closure|string|array  $title
     * @param  \Closure|null  $warp
     */
    public function __construct($title, \Closure $warp = null)
    {
        if ($title instanceof \Closure) {
            $warp = $title;
            $title = ['lte.add', 'lte.id_edit'];
        }

        if (is_array($title)) {

            $title = lte_model_type('create') ?
                (isset($title[0]) ? $title[0] : 'lte.add') :
                (isset($title[1]) ? $title[1] : 'lte.id_edit');
        }

        parent::__construct(function (DIV $div) use ($title, $warp) {
            $card = null;
            $div->card($title)->haveLink($card)
                ->defaultTools()
                ->bodyForm(function (Form $form) use ($warp, $card) {
                    if ($warp) {
                        ccc($warp, [
                            Form::class => $form,
                            Card::class => $card,
                            static::class => $this
                        ]);
                    }
                })->footerForm();
        });
    }
}