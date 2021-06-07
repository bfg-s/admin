<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\LteAdmin\Segments\Tagable\Cores\CoreNestable;
use Lar\Tagable\Events\onRender;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 * @mixin NestedMacroList
 */
class Nested extends DIV implements onRender {

    use Macroable, Piplineble;

    /**
     * @var bool
     */
    protected $only_content = true;

    /**
     * @var CoreNestable
     */
    protected $nested;

    /**
     * Nested constructor.
     * @param  null|array|Model|\Closure  $model
     * @param  null|array|\Closure  $instructions
     * @param  mixed  ...$params
     * @throws \ReflectionException
     */
    public function __construct($model = null, $instructions = [], ...$params)
    {
        parent::__construct();

        if (is_embedded_call($model)) {

            $params[] = $model;
            $model = null;
        }

        if (is_embedded_call($instructions)) {

            $params[] = $instructions;
            $instructions = [];
        }

        if (is_array($model)) {

            $instructions = $model;
            $model = null;
        }

        if (!$model) {

            $model = gets()->lte->menu->model;
        }

        $model = static::fire_pipes($model, get_class($model));

        $this->nested = new CoreNestable($model, $instructions);

        $this->when($params);

        $this->appEnd($this->nested);

        $this->callConstructEvents();
    }

    /**
     * @param  string|null  $field
     * @return $this
     */
    public function orderDesc(string $field = null)
    {
        $this->nested->orderDesc($field);

        return $this;
    }

    /**
     * @param  string|null  $field
     * @param  string|null  $order
     * @return $this
     */
    public function orderBy(string $field = null, string $order = null)
    {
        $this->nested->orderBy($field, $order);

        return $this;
    }

    /**
     * @param  string|callable  $field
     * @return $this
     */
    public function titleField($field)
    {
        $this->nested->title_field($field);

        return $this;
    }

    /**
     * @param  int  $depth
     * @return $this
     */
    public function maxDepth(int $depth)
    {
        $this->nested->maxDepth($depth);

        return $this;
    }

    /**
     * @param  callable  $call
     * @return $this
     */
    public function controls(callable $call) {

        $this->nested->controls($call);

        return $this;
    }

    /**
     * @param  \Closure|array|null  $test
     * @return $this
     */
    public function disableControls($test = null)
    {
        $this->nested->disableControls($test);

        return $this;
    }

    /**
     * @param  \Closure|array|null  $test
     * @return $this
     */
    public function disableInfo($test = null)
    {
        $this->nested->disableInfo($test);

        return $this;
    }

    /**
     * @param  \Closure|array|null  $test
     * @return $this
     */
    public function disableEdit($test = null)
    {
        $this->nested->disableEdit($test);

        return $this;
    }

    /**
     * @param  \Closure|array|null  $test
     * @return $this
     */
    public function disableDelete($test = null)
    {
        $this->nested->disableDelete($test);

        return $this;
    }

    /**
     * @return mixed|void
     * @throws \ReflectionException
     */
    public function onRender()
    {
        $this->callRenderEvents();

        $this->nested->build();
    }
}
