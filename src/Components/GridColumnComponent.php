<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\Traits\BuildHelperTrait;
use Lar\LteAdmin\Components\Traits\FieldMassControlTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\Tagable\Events\onRender;

/**
 * @methods Lar\LteAdmin\Components\FieldComponent::$form_components (string $name, string $label = null, ...$params)
 * @mixin GridColumnComponentMacroList
 * @mixin GridColumnComponentMethods
 */
class GridColumnComponent extends DIV implements onRender
{
    use FieldMassControlTrait, Macroable, BuildHelperTrait, Delegable;

    /**
     * @var string
     */
    protected $class = 'col-md';

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
     * @return bool|GridColumnComponent|\Lar\LteAdmin\Components\FormGroupComponent|\Lar\Tagable\Tag|mixed|string
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
     */
    public function onRender()
    {
        $this->addClass($this->class);

        $this->callRenderEvents();
    }
}
