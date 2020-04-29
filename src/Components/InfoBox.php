<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\SPAN;

/**
 * Class InfoBox
 * @package Lar\LteAdmin\Components
 */
class InfoBox extends DIV
{
    /**
     * @var string[]
     */
    protected $props = [
        'info-box'
    ];

    /**
     * @var SPAN
     */
    protected $icon_bg;

    /**
     * Col constructor.
     * @param $text
     * @param  null  $number
     * @param  string  $icon
     * @param  mixed  ...$params
     */
    public function __construct($text, $number = null, $icon = 'fas fa-info-circle', ...$params)
    {
        parent::__construct();

        $this->span(['info-box-icon elevation-1'])->haveLink($this->icon_bg)->i([$icon]);

        $content = $this->div(['info-box-content']);

        $content->span(['info-box-text'], $text);

        if (!is_array($number)) $number = [$number];

        $content->span(['info-box-number'], ($number[0] ?? ''))->small($number[1] ?? '');

        $this->when($params);
    }

    /**
     * @param  string  $bg
     * @return $this
     */
    public function icon_bg(string $bg)
    {
        $this->icon_bg->addClass($bg);

        return $this;
    }
}