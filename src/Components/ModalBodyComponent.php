<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\Traits\BuildHelperTrait;
use Lar\LteAdmin\Components\Traits\FieldMassControlTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Tagable\Events\onRender;

/**
 * @methods Lar\LteAdmin\Components\FieldComponent::$inputs (string $name, string $label = null, ...$params)
 * @mixin ModalBodyComponentMacroList
 * @mixin ModalBodyComponentMethods
 */
class ModalBodyComponent extends DIV implements onRender
{
    use FieldMassControlTrait, Macroable, BuildHelperTrait, Delegable;

    /**
     * @var string[]
     */
    protected $props = [
        'modal-body',
    ];

    /**
     * @var ModalContent
     */
    protected $content_parent;

    /**
     * ModalBody constructor.
     * @param  ModalComponent  $content_parent
     * @param  mixed  ...$params
     * @throws \ReflectionException
     */
    public function __construct(ModalComponent $content_parent, ...$params)
    {
        parent::__construct();

        $this->when($params);

        $this->content_parent = $content_parent;

        $this->callConstructEvents();
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FormComponent|\Lar\Tagable\Tag|mixed|string
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
