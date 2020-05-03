<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;

/**
 * Class SmallBox
 * @package Lar\LteAdmin\Components
 */
class SmallBox extends DIV
{
    /**
     * @var string[]
     */
    protected $props = [
        'small-box'
    ];

    /**
     * Col constructor.
     * @param  null  $data
     * @param  null  $link
     * @param  string  $icon
     * @param  mixed  ...$params
     */
    public function __construct($data, $link = null, $icon = 'fas fa-info-circle', ...$params)
    {
        parent::__construct();

        if (!is_array($data)) $data = [$data];

        $count = $data[0];

        if (is_string($count)) $count = explode(" ", $count);
        else if (is_numeric($count)) $count = explode(".", $count);

        $inner = $this->div(['inner']);

        $inner->h3()
            ->text(' ' . ($count[0] ?? ''))
            ->sup(['style' => 'font-size: 20px'])->text(' ' . ($count[1] ?? ''));

        if ($inner && isset($data[1])) $inner->p($data[1]);

        if ($icon) $this->div(['icon'])->i([$icon]);

        if ($link) {
            $link = !is_array($link) ? [$link] : $link;
            $a = $this->a(['small-box-footer'])->setHrefIf(isset($link[0]), $link[0]);
            $a->text($link[1] ?? __('lte::admin.more_info'), ':space');
            $a->i([$link[2] ?? 'fas fa-arrow-circle-right']);
        }

        $this->when($params);
    }
}