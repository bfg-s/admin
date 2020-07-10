<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Segments\Tagable\Traits\BuildHelperTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\FieldMassControl;
use Lar\Tagable\Events\onRender;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 * @methods Lar\LteAdmin\Segments\Tagable\Field::$form_components (string $name, string $label = null, ...$params)
 * @mixin ColMacroList
 * @mixin ColMethods
 */
class Col extends DIV implements onRender {

    use FieldMassControl, Macroable, BuildHelperTrait;

    /**
     * @var string
     */
    protected $class = 'col-md';

    /**
     * Col constructor.
     * @param int|\Closure $num
     * @param  mixed  ...$params
     */
    public function __construct($num = null, ...$params)
    {
        parent::__construct();

        if (is_numeric($num)) {

            $this->class .= "-{$num}";

        } else if ($num) {

            $params[] = $num;
        }

        $this->when($params);

        $this->addClass($this->class);

        $this->callConstructEvents();
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|Form|\Lar\Tagable\Tag|mixed|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if ($call = $this->call_group($name, $arguments)) {

            return $call;
        }

        return parent::__call($name, $arguments);
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