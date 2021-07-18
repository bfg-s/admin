<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\SPAN;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Segments\Tagable\Traits\BuildHelperTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\FieldMassControl;
use Lar\Tagable\Events\onRender;

/**
 * Class Live
 * @package Lar\LteAdmin\Segments\Tagable
 * @methods Lar\LteAdmin\Segments\Tagable\Field::$form_components (string $name, string $label = null, ...$params)
 * @mixin ColMacroList
 * @mixin ColMethods
 */
class Live extends SPAN implements onRender {

    use FieldMassControl, Macroable, BuildHelperTrait;

    /**
     * @var int
     */
    protected static $counter = 0;

    /**
     * Live constructor.
     * @param ...$params
     */
    public function __construct($condition, ...$params)
    {
        parent::__construct();

        if ($condition instanceof \Closure) {

            $params[] = $condition;
            $condition = true;
        }

        if ($condition) {

            $this->when($params);
        }

        $this->addClass('__live__')
            ->setId('live-' . static::$counter);

        static::$counter++;

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
