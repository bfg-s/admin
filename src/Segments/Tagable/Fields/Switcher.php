<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;


use Lar\Layout\Abstracts\Component;

/**
 * Class Email
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Switcher extends Input
{
    /**
     * @var string
     */
    protected $type = "checkbox";

    /**
     * @var string
     */
    protected $icon = null;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'switch'
    ];

    /**
     * Switcher constructor.
     * @param  Component  $parent
     * @param  string  $title
     * @param  string  $name
     * @param  mixed  ...$params
     */
    public function __construct(Component $parent, string $title, string $name, ...$params)
    {
        parent::__construct($parent, $title, $name, $params);

        $this->data['on-text'] = __('lte.on');
        $this->data['off-text'] = __('lte.off');
    }

    /**
     * @param  string  $size
     * @return $this
     */
    public function size(string $size)
    {
        $this->data['size'] = $size;

        return $this;
    }

    /**
     * @param  string  $on
     * @param  string  $off
     * @param  string  $label
     * @return $this
     */
    public function labels(string $on = null, string $off = null, string $label = null)
    {
        if ($on) $this->data['on'] = $on;
        if ($off) $this->data['off'] = $off;
        if ($label) $this->data['label'] = $label;

        return $this;
    }
}