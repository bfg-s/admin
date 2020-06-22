<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\Tabs;

/**
 * Class Container
 * @package App\LteAdmin\Segments
 */
class MatrixTabs extends Container {

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
            /** @var Card $card */
            $card = null;
            $div->card($title)->haveLink($card)
                ->defaultTools()
                ->bodyForm(function (Form $form) use ($warp, $card) {
                    $card->getBody()->p0();
                    $form->tabs(function (Tabs $tabs) use ($warp, $card, $form) {
                        if ($warp) {
                            $warp($tabs, $form, $card, $this);
                        }
                    });
                })->footerForm();
        });
    }
}