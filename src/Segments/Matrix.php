<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Form;

/**
 * Class Container
 * @package App\LteAdmin\Segments
 */
class Matrix extends Container {

    /**
     * Matrix constructor.
     * @param  \Closure|string  $title
     * @param  \Closure|null  $warp
     */
    public function __construct($title, \Closure $warp = null)
    {
        if ($title instanceof \Closure) {
            $warp = $title;
            $title = lte_model_type('create') ? 'lte.add' : 'lte.id_edit';
        }

        parent::__construct(function (DIV $div) use ($title, $warp) {
            $card = null;
            $div->card($title)->haveLink($card)
                ->defaultTools()
                ->bodyForm(function (Form $form) use ($warp, $card) {
                    if ($warp) {
                        $warp($form, $card, $this);
                    }
                })->footerForm();
        });
    }
}