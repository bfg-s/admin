<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Tagable\Events\onRender;

class DividerComponent extends DIV implements onRender
{
    use Macroable;

    /**
     * @var string
     */
    protected $class = 'row';

    /**
     * @param  string|callable|null  $right_title
     * @param  string|callable|null  $center_title
     * @param  string|callable|null  $left_title
     * @param ...$params
     */
    public function __construct(
        $right_title = null,
        $center_title = null,
        $left_title = null,
        ...$params
    ) {
        parent::__construct();

        if ($left_title) {
            if (is_string($left_title)) {
                $this->div(['col-auto'])->h4($left_title)->textSecondary();
            } else {
                $this->div(['col-auto'])->when($left_title);
            }
        }

        if ($center_title) {
            $this->div(['col'])->hr();

            if (is_string($center_title)) {
                $this->div(['col-auto'])->h4($center_title)->textSecondary();
            } else {
                $this->div(['col-auto'])->when($center_title);
            }

            $this->div(['col'])->hr();
        } else {
            $this->div(['col'])->hr();
        }

        if ($right_title) {
            if (is_string($right_title)) {
                $this->div(['col-auto'])->h4($right_title)->textSecondary();
            } else {
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
