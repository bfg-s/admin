<?php

namespace Lar\LteAdmin\Components;

use Exception;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\Traits\BuildHelperTrait;
use Lar\LteAdmin\Components\Traits\FieldMassControlTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Tagable\Core\Extension\Content;
use Lar\Tagable\Events\onRender;
use Lar\Tagable\Tag;
use ReflectionException;

/**
 * @methods Lar\LteAdmin\Components\FieldComponent::$inputs (string $name, string $label = null, ...$params)
 * @mixin LangComponentMacroList
 * @mixin LangComponentMethods
 */
class LangComponent extends DIV implements onRender
{
    use FieldMassControlTrait, Macroable, BuildHelperTrait, Delegable;

    /**
     * @var array|null
     */
    protected $lang_list = null;

    /**
     * Lang constructor.
     * @param  array|null  $lang_list
     * @param ...$params
     */
    public function __construct(array $lang_list = null, ...$params)
    {
        $this->lang_list = $lang_list;

        $this->toExecute('make_lang');

        parent::__construct();

        $this->when($params);

        $this->callConstructEvents();
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FormComponent|Tag|mixed|string
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
     * Make languages inputs.
     */
    public function make_lang()
    {
        $inner = [];

        foreach ($this->content as $inner_input) {
            $inner_input = $inner_input->getOriginalValue();

            if (is_object($inner_input) && $inner_input instanceof FormGroupComponent) {
                $inn = [];
                $inner_input->unregister();
                foreach (array_values($this->lang_list ?: config('layout.languages', [])) as $lang) {
                    $input = clone $inner_input;
                    $input->set_name($input->get_name()."[{$lang}]");
                    $input->set_path($input->get_path().".{$lang}");
                    $input->set_id($input->get_id()."_{$lang}");
                    $input->set_title($input->get_title().' ['.strtoupper($lang).']');
                    $inn[] = $input->render();
                }

                $inner[] = new Content($inn[array_key_last($inn)], $this);
            } else {
                $inner[] = new Content($inner_input, $this);
            }
        }

        $this->content->setItems($inner);
    }

    /**
     * @return mixed|void
     * @throws ReflectionException
     */
    public function onRender()
    {
        $this->callRenderEvents();
    }
}
