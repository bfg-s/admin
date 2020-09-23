<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Tagable\Events\onRender;

/**
 * Class Divider
 * @package Lar\LteAdmin\Segments\Tagable
 */
class Divider extends DIV implements onRender {

    use Macroable;

    /**
     * @var string
     */
    protected $class = 'row';

    /**
     * Col constructor.
     * @param  \Closure|string|array|null  $title
     * @param  mixed  ...$params
     * @throws \ReflectionException
     */
    public function __construct($title = null, ...$params)
    {
        parent::__construct();

        if ($title) {

            $this->div(['col-auto'])->h4($title);
        }

        $this->div(['col'])->hr();

        $this->when($params);

        $this->addClass($this->class);

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