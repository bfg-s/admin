<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Segments\Modal;
use Lar\LteAdmin\Segments\Tagable\Traits\BuildHelperTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\FieldMassControl;
use Lar\Tagable\Events\onRender;

/**
 * Class ModalBody
 * @package Lar\LteAdmin\Segments\Tagable
 * @methods Lar\LteAdmin\Segments\Tagable\Field::$form_components (string $name, string $label = null, ...$params)
 * @mixin ModalBodyMacroList
 * @mixin ModalBodyMethods
 */
class ModalBody extends DIV implements onRender {

    use FieldMassControl, Macroable, BuildHelperTrait;

    /**
     * @var string[]
     */
    protected $props = [
        'modal-body'
    ];

    /**
     * @var ModalContent
     */
    protected $content_parent;

    /**
     * ModalBody constructor.
     * @param  Modal  $content_parent
     * @param  mixed  ...$params
     * @throws \ReflectionException
     */
    public function __construct(Modal $content_parent, ...$params)
    {
        parent::__construct();

        $this->when($params);

        $this->content_parent = $content_parent;

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