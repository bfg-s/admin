<?php

namespace Lar\LteAdmin\Components;

use Illuminate\Database\Eloquent\Relations\Relation;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\Traits\BuildHelperTrait;
use Lar\LteAdmin\Components\Traits\FieldMassControlTrait;
use Lar\LteAdmin\Components\Traits\ModelRelation\ModelRelationBuilderTrait;
use Lar\LteAdmin\Components\Traits\ModelRelation\ModelRelationHelpersTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;

/**
 * @methods Lar\LteAdmin\Components\FieldComponent::$form_components (string $name, string $label = null, ...$params)
 * @mixin ModelRelationComponentMacroList
 * @mixin ModelRelationComponentMethods
 */
class ModelRelationComponent extends DIV
{
    use FieldMassControlTrait,
        Macroable,
        BuildHelperTrait,
        ModelRelationHelpersTrait,
        ModelRelationBuilderTrait,
        Delegable;

    /**
     * @var array
     */
    protected $model_instruction = [];

    /**
     * @var Relation
     */
    protected $relation;

    /**
     * @var string
     */
    protected $relation_name;

    /**
     * @var string
     */
    protected $path_name;

    /**
     * @var \Closure|array
     */
    protected $create_content;

    /**
     * @var ModelRelationContentComponent
     */
    protected $last_content;

    /**
     * @var callable
     */
    protected $on_empty;

    /**
     * @var mixed
     */
    protected static $fm;
    protected $fm_old;

    /**
     * ModelRelation constructor.
     * @param  string|array  $relation
     * @param  array|\Closure  $instructions
     * @param  callable|null  $content
     * @param  mixed  ...$params
     */
    public function __construct($relation, $instructions, callable $content = null, ...$params)
    {
        parent::__construct();

        if (is_callable($instructions)) {
            $content = $instructions;
            $instructions = [];
        }

        if (is_array($instructions)) {
            $this->model($instructions);
        }

        if (is_array($relation)) {
            $this->relation_name = $relation[0];
            $relation = $relation[1];
        } else {
            $this->relation_name = $relation;
        }

        if (FormComponent::$current_model) {
            $m = FormComponent::$current_model;
        } else {
            $m = gets()->lte->menu->model;
        }

        if (is_object($m)) {
            $relation = $m->{$relation}();
        }

        if (! ($relation instanceof Relation)) {
            $this->alert('Danger!', 'Relation not found!')->danger();
        } else {
            $this->fm_old = self::$fm;
            self::$fm = $relation;
            $this->relation = $relation;
            $this->toExecute('_build');
            $this->create_content = $content;
            $this->setDatas(['relation' => $this->relation_name, 'relation-path' => $this->relation_name]);
        }

        $this->when($params);

        $this->callConstructEvents();
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FormGroupComponent|ModelRelationComponent|\Lar\Tagable\Tag|mixed|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if ($call = $this->call_group($name, $arguments)) {
            return $call;
        }

        return parent::__call($name, $arguments);
    }
}
