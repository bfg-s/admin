<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits\ModelRelation;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Components\Template;
use Lar\Layout\Components\TemplateArea;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\HR;
use Lar\Layout\Tags\INPUT;
use Lar\LteAdmin\Core\ModelSaver;
use Lar\LteAdmin\Segments\Tagable\ButtonGroup;
use Lar\LteAdmin\Segments\Tagable\Col;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\FormGroup;
use Lar\LteAdmin\Segments\Tagable\ModelRelationContent;

/**
 * Trait ModelRelationBuilderTrait
 * @package Lar\LteAdmin\Segments\Tagable\Traits\ModelRelation
 * @mixin Component
 */
trait ModelRelationBuilderTrait {

    /**
     * Build relation rows
     */
    protected function _build()
    {
        $this->relation = eloquent_instruction($this->relation, $this->model_instruction);

        $old_model_form = Form::$current_model;

        $relation = $this->relation_name;

        $dr = $this->_relation_path();

        FormGroup::$construct_modify['build_relation'] = function (FormGroup $group, Model $model) use ($relation, $dr) {
            $group->set_name("{$dr}[".$model->{$model->getKeyName()}."][{$group->get_name()}]");
            $group->set_id(implode("_", static::$depth) . "_{$group->get_id()}_" . $model->{$model->getKeyName()});
        };

        /** @var Model $item */
        foreach ($this->relation->get() as $item) {
            Form::$current_model = $item;
            $container = ModelRelationContent::create($relation, $item->{$item->getKeyName()});
            $container->appEnd(
                INPUT::create()->setType('hidden')
                    ->setName("{$dr}[".$item->{$item->getKeyName()}."][{$item->getKeyName()}]")
                    ->setValue($item->{$item->getKeyName()})
            );
            $this->last_content = ModelRelationContent::create($relation, 'template_content', 'template_content');
            $container->appEnd($this->last_content);
            $this->_call_tpl($this->last_content, $item, $this);
            if ($this->last_content->get_test_var('control_group', [$item])) {
                $container->col()->textRight()->p0()->when(function (Col $col) use ($relation, $item) {

                    $del = $this->last_content->get_test_var('control_delete', [$item]);

                    $col->button_group(function (ButtonGroup $group) use ($relation, $item, $del) {

                        $this->last_content->callControls($group, $item);

                        if ($del) {
                            $group->danger(['fas fa-trash', __('lte.delete')])
                                ->on_click('lte::drop_relation', [
                                    INPUT::create()->setType('hidden')->addClass('delete_field')
                                        ->setName("{$this->relation_path}[".$item->{$item->getKeyName()}."][".ModelSaver::DELETE_FIELD."]")
                                        ->setValue($item->{$item->getKeyName()})->render()
                                ]);
                        }
                    })->addCLass('control_relation');

                    if ($this->last_content->get_test_var('control_restore') && $del) {
                        $col->divider(null, function (DIV $div) use ($item) {
                            $div->button_group(function (ButtonGroup $group) use ($item) {
                                $group->secondary([
                                    'fas fa-redo',
                                    __('lte.restore_subject', ['subject' => strtoupper($item->getKeyName()).': '.$item->{$item->getKeyName()}])
                                ])
                                    ->on_click('lte::return_relation');
                            });
                        })->hide()->addClass('return_relation');
                    }

                });
            }
            $container->hr(['style' => 'border-top: 0;']);
            $this->appEnd($container);
        }

        Form::$current_model = $old_model_form;

        unset(FormGroup::$construct_modify['build_relation']);

        $this->appEnd(TemplateArea::create("relation_{$this->path_name}_template"));

        unset(static::$depth[$this->key]);
    }

    /**
     * Build relation template maker button
     * @return string
     */
    protected function _btn()
    {
        if (!$this->last_content->get_test_var('control_create')) {

            return "";
        }

        $old_model_form = Form::$current_model;

        $relation = $this->relation_name;

        FormGroup::$construct_modify['build_relation'] = function (FormGroup $group) use ($relation) {
            $group->set_name($this->relation_path . "[{__id__}][{$group->get_name()}]");
            $group->set_id(time() . "_".implode("_", static::$depth)."_{$group->get_id()}");
        };

        Form::$current_model = null;
        $container = ModelRelationContent::create($relation, 'template_container');
        $this->last_content = ModelRelationContent::create($relation, 'template_content', 'template_content');
        $container->appEnd($this->last_content);
        $this->_call_tpl($this->last_content, $this->relation->getQuery()->getModel(), $this);
        $container->col()->textRight()->p0()->button_group(function (ButtonGroup $group) use ($relation) {
            $group->setStyle("margin-left: 0!important;");
            $group->warning(['fas fa-minus', __('lte.remove')])->on_click('lte::drop_relation_tpl');
        });
        $container->hr(['style' => 'border-top: 0;']);

        Form::$current_model = $old_model_form;
        unset(FormGroup::$construct_modify['build_relation']);

        $hr = HR::create();
        $row = $hr->row();
        $row->col()->textRight()->button_group(function (ButtonGroup $group) use ($relation) {
            $group->success(['fas fa-plus', __('lte.add')])->on_click('lte::add_relation_tpl', "relation_{$this->path_name}_template");
        });
        $row->appEnd(Template::create("relation_{$this->path_name}_template")->appEnd($container));
        return $hr->render();
    }

    /**
     * @param  mixed  ...$params
     * @return mixed
     */
    protected function _call_tpl(...$params)
    {
        if ($t = $this->_path_name()) {
            $this->path_name = $t;
        }

        $return = call_user_func($this->create_content, ...$params);

        return $return;
    }

    /**
     * @return string
     */
    protected function _relation_path()
    {
        $depth = static::$depth;
        $first_key = array_key_first($depth);
        if (!isset($depth[$first_key])) return "";
        $first = $depth[$first_key];
        unset($depth[$first_key]);
        $this->relation_path = $first . (count($depth) ? "[" . implode("][", $depth) . "]" : '');
        return $this->relation_path;
    }

    /**
     * Build relation template maker
     * @param  Component  $component
     */
    protected function _tpl(Component $component)
    {
        $component->appEndToRendered($this->_btn());
    }

    /**
     * @return string
     */
    protected function _path_name()
    {
        return implode('_', static::$depth);
    }
}