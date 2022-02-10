<?php

namespace LteAdmin\Components;

use Exception;
use Lar\Layout\Tags\DIV;
use Lar\Tagable\Events\onRender;
use Lar\Tagable\Tag;
use LteAdmin\Explanation;
use LteAdmin\Traits\BuildHelperTrait;
use LteAdmin\Traits\Delegable;
use LteAdmin\Traits\FieldMassControlTrait;
use LteAdmin\Traits\Macroable;

/**
 * @methods LteAdmin\Components\FieldComponent::$inputs (string $name, string $label = null, ...$params)
 * @mixin GridColumnComponentMacroList
 * @mixin GridColumnComponentMethods
 */
class GridColumnComponent extends DIV implements onRender
{
    use FieldMassControlTrait;
    use Macroable;
    use BuildHelperTrait;
    use Delegable;

    /**
     * @var string
     */
    protected $class = 'pl-0 col-md';

    /**
     * @param  array  $delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->explainForce(Explanation::new($delegates));

        $this->callConstructEvents();
    }

    public function num(int $num)
    {
        $this->class .= "-{$num}";

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|GridColumnComponent|FormGroupComponent|Tag|mixed|string
     * @throws Exception
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
     */
    public function onRender()
    {
        $this->addClass($this->class);

        $this->callRenderEvents();
    }
}
