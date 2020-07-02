<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Tagable\Events\onRender;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 */
class Row extends DIV implements onRender {

    use Macroable;

    /**
     * @var string[]
     */
    protected $props = [
        'row'
    ];

    /**
     * Row constructor.
     * @param  mixed  ...$params
     */
    public function __construct(...$params)
    {
        parent::__construct();

        $this->when($params);

        $this->callConstructEvents();
    }

    /**
     * @return mixed|void
     * @throws \ReflectionException
     */
    public function onRender()
    {
        $this->callRenderEvents();
    }
}