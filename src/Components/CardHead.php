<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;

class CardHead extends DIV
{

    /**
     * @var array
     */
    protected $props = [
        'card-header'
    ];

    /**
     * CardHead constructor.
     * @param  string|null  $title
     * @param  string|null  $icon
     * @param  mixed  ...$params
     */
    public function __construct(string $title = null, string $icon = null,  ...$params)
    {
        parent::__construct();

        if ($title) {

            $title_obj = $this->h3(['card-title']);

            if ($icon) {

                $title_obj->text("<i class=\"{$icon} mr-1\"></i>");
            }

            $model = gets()->lte->menu->model;

            if ($model) {

                $attrs = $model->getAttributes();

                foreach ($attrs as $key => $attr) {

                    if (is_string($attr) || is_numeric($attr)) {
                        $title = str_replace(":{$key}", $attr, $title);
                    }
                }
            }

            $title_obj->text($title);
        }

        $this->when($params);
    }
}