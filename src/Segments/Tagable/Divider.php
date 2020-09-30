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
     * Divider constructor.
     * @param  mixed|null  $left_title
     * @param  mixed|null  $right_title
     * @param  mixed  ...$params
     */
    public function __construct($left_title = null, $right_title = null, ...$params)
    {
        parent::__construct();

        if ($left_title) {

            if (is_string($left_title)) {

                $this->div(['col-auto'])->h4($left_title);
            }

            else {

                $this->div(['col-auto'])->when($left_title);
            }
        }

        $this->div(['col'])->hr();

        if ($right_title) {

            if (is_string($right_title)) {

                $this->div(['col-auto'])->h4($right_title);
            }

            else {

                $this->div(['col-auto'])->when($right_title);
            }
        }

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