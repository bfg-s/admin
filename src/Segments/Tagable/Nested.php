<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Cores\CoreNestable;
use Lar\Tagable\Events\onRender;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 */
class Nested extends DIV implements onRender {

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
     * @param  null|Model|\Closure  $model
     * @param  null|array|\Closure  $instructions
     * @param  mixed  ...$params
     * @throws \ReflectionException
     */
    public function __construct($model = null, $instructions = [], ...$params)
    {
        parent::__construct();

        if ($model instanceof \Closure) {

            $params[] = $model;
            $model = null;
        }

        if ($instructions instanceof \Closure) {

            $params[] = $instructions;
            $instructions = [];
        }

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
     * @param  string  $field
     * @return $this
     */
    public function titleField(string $field)
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
     * @param  \Closure|null  $test
     * @return $this
     */
    public function disableControls(\Closure $test = null)
    {
        $this->nested->disableControls($test);

        return $this;
    }

    /**
     * @param  \Closure|null  $test
     * @return $this
     */
    public function disableInfo(\Closure $test = null)
    {
        $this->nested->disableInfo($test);

        return $this;
    }

    /**
     * @param  \Closure|null  $test
     * @return $this
     */
    public function disableEdit(\Closure $test = null)
    {
        $this->nested->disableEdit($test);

        return $this;
    }

    /**
     * @param  \Closure|null  $test
     * @return $this
     */
    public function disableDelete(\Closure $test = null)
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