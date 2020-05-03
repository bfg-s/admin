<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\INPUT;

/**
 * Class Switcher
 * @package Lar\LteAdmin\Components
 */
class Switcher extends INPUT
{

    /**
     * @var array
     */
    protected $props = [
        'type' => 'checkbox',
        'value' => 1
    ];

    /**
     * Col constructor.
     * @param  array  $props
     * @param  mixed  ...$params
     */
    public function __construct($props = [], ...$params)
    {
        parent::__construct();

        $on_text = $props[0] ?? __('lte::admin.on');
        $off_text = $props[1] ?? __('lte::admin.off');

        $this->attr(['data' => ['load' => 'switch', 'on-text' => $on_text, 'off-text' => $off_text]]);

        $this->when($params);
    }
}