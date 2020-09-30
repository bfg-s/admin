<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Illuminate\Database\Eloquent\Relations\Relation;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Segments\Tagable\Traits\BuildHelperTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\FieldMassControl;
use Lar\LteAdmin\Segments\Tagable\Traits\ModelRelation\ModelRelationBuilderTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\ModelRelation\ModelRelationHelpersTrait;
use Lar\Tagable\Events\onRender;

/**
 * Class ModelRelation
 * @package Lar\LteAdmin\Segments\Tagable
 * @methods Lar\LteAdmin\Segments\Tagable\Field::$form_components (string $name, string $label = null, ...$params)
 * @mixin ModelRelationMacroList
 * @mixin ModelRelationMethods
 */
class ModelRelation extends DIV implements onRender {

    use FieldMassControl,
        Macroable,
        BuildHelperTrait,
        ModelRelationHelpersTrait,
        ModelRelationBuilderTrait;

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
     * @var string
     */
    protected $relation_path;

    /**
     * @var \Closure|array
     */
    protected $create_content;

    /**
     * @var int
     */
    protected $key;

    /**
     * @var ModelRelationContent
     */
    protected $last_content;

    /**
     * @var array
     */
    static $depth = [];

    /**
     * ModelRelation constructor.
     * @param  string  $relation
     * @param  array|\Closure  $instructions
     * @param  callable|null  $content
     * @param  mixed  ...$params
     */
    public function __construct(string $relation, $instructions, callable $content = null, ...$params)
    {
        parent::__construct();

        if (is_callable($instructions)) {

            $content = $instructions;
            $instructions = [];
        }

        if (is_array($instructions)) {

            $this->model($instructions);
        }

        $this->relation_name = $relation;

        if (Form::$current_model) {

            $m = Form::$current_model;
        }

        else {
            $m = gets()->lte->menu->model;
        }

        if (is_object($m) && method_exists($m, $relation)) {
            $relation = $m->{$relation}();
        }

        if (!($relation instanceof Relation)) {
            $this->alert("Danger!", "Relation not found!")->danger();
        }

        else {

            static::$depth[] = $this->relation_name;

            $this->key = array_key_last(static::$depth);

            $this->relation = $relation;
            $this->toExecute('_build');
            $this->create_content = $content;
            $this->setDatas(['relation' => $this->relation_name]);
        }

        $this->when($params);

        $this->callConstructEvents();
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FormGroup|ModelRelation|\Lar\Tagable\Tag|mixed|string
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
        $this->rendered(function ($d) {
            $this->_tpl($d);
        });
        $this->callRenderEvents();
    }
}