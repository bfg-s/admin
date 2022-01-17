<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits\ModelRelation;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Components\Template;
use Lar\Layout\Components\TemplateArea;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\INPUT;
use Lar\LteAdmin\Core\ModelSaver;
use Lar\LteAdmin\Segments\Tagable\ButtonGroup;
use Lar\LteAdmin\Segments\Tagable\Col;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\FormGroup;
use Lar\LteAdmin\Segments\Tagable\ModelRelation;
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

        FormGroup::$construct_modify['build_relation'] = function (FormGroup $group, Model $model) {
            $k = $model->{$model->getKeyName()};
            $n = $group->get_name();
            $m = [];
            preg_match('/([a-zA-Z\-\_]+)(\[.*\])?/', $n, $m);
            $group->set_name("{$this->relation_name}[{$k}][{$m[1]}]".($m[2]??''));
            $group->set_id("{$this->relation_name}_{$group->get_id()}_{$k}");
        };

        $datas = $this->relation->get();

        /** @var Model $item */
        foreach ($datas as $item) {
            Form::$current_model = $item;
            $container = ModelRelationContent::create($this->relation_name, $item->{$item->getKeyName()});
            $container->appEnd(
                INPUT::create()->setType('hidden')
                    ->setName("{$this->relation_name}[".$item->{$item->getKeyName()}."][{$item->getKeyName()}]")
                    ->setValue($item->{$item->getKeyName()})
            );
            $this->last_content = ModelRelationContent::create($this->relation_name, 'template_content', 'template_content');
            $container->appEnd($this->last_content);
            $this->_call_tpl($this->last_content, $item, $this);
            if ($this->last_content->get_test_var('control_group', [$item])) {

                $del = $this->last_content->get_test_var('control_delete', [$item]);

                if ($del || $this->last_content->hasControls()) {

                    $container->col()->textRight()->m0()->p0()->when(function (Col $col) use (
                        $item, $del
                    ) {
                        $col->button_group(function (ButtonGroup $group) use ($item, $del) {

                            $this->last_content->callControls($group, $item);

                            if ($del) {

                                $group->danger(['fas fa-trash', __('lte.delete')])
                                    ->on_click('lte::drop_relation', [
                                        INPUT::create()->setType('hidden')->addClass('delete_field')
                                            ->setName("{$this->relation_name}[".$item->{$item->getKeyName()}."][".ModelSaver::DELETE_FIELD."]")
                                            ->setValue($item->{$item->getKeyName()})->render()
                                    ]);
                            }

                        })->addCLass('control_relation');

                        if ($this->last_content->get_test_var('control_restore') && $del) {
                            $col->divider(null, null, function (DIV $div) use ($item) {
                                $div->button_group(function (ButtonGroup $group) use ($item) {
                                    $text_d = $this->last_content->get_test_var('control_restore_text');
                                    $s = $text_d ? $text_d : (strtoupper($item->getKeyName()).': '.$item->{$item->getKeyName()});
                                    $text = __('lte.restore_subject', ['subject' => $s]);
                                    $group->secondary([
                                        'fas fa-redo',
                                        tag_replace($text, $item)
                                    ])
                                        ->on_click('lte::return_relation');
                                });
                            })->hide()->addClass('return_relation');
                        }
                    });
                }
            }
            $container->hr(['style' => 'border-top: 0;']);
            $this->appEnd($container);
        }

        if (!$datas->count() && $this->on_empty) {

            $container = ModelRelationContent::create($this->relation_name, 'empty', 'template_empty_container');
            $this->last_content = ModelRelationContent::create($this->relation_name, 'template_empty_content', 'template_empty_content');
            $this->_call_empty_tpl($this->last_content, $this->relation->getQuery()->getModel(), $this);
            $container->appEnd($this->last_content);
            $this->appEnd($container);
        }

        Form::$current_model = $old_model_form;

        unset(FormGroup::$construct_modify['build_relation']);

        $this->appEnd(TemplateArea::create("relation_{$this->relation_name}_template"));

        $this->_btn();

        $this->callRenderEvents();

        ModelRelation::$fm = $this->fm_old;
    }

    /**
     * Build relation template maker button
     * @return string
     */
    protected function _btn()
    {
        $old_model_form = Form::$current_model;

        FormGroup::$construct_modify['build_relation'] = function (FormGroup $group) {
            $m = [];
            preg_match('/([a-zA-Z\-\_]+)(\[.*\])?/', $group->get_name(), $m);
            $group->set_name("{$this->relation_name}[{__id__}][{$m[1]}]".($m[2]??''));
            $group->set_id("{__id__}_{$this->relation_name}_{$group->get_id()}");
        };

        Form::$current_model = null;
        $container = ModelRelationContent::create($this->relation_name, 'template_container');
        $this->last_content = ModelRelationContent::create($this->relation_name, 'template_content', 'template_content');
        $container->appEnd($this->last_content);
        Form::$current_model = $this->relation->getQuery()->getModel();
        $this->_call_tpl($this->last_content, $this->relation->getQuery()->getModel(), $this);
        Form::$current_model = null;
        if (!$this->last_content->get_test_var('control_create')) { return ""; }
        $container->col()->textRight()->p0()->button_group(function (ButtonGroup $group) {
            $group->setStyle("margin-left: 0!important;");
            $group->warning(['fas fa-minus', __('lte.remove')])->on_click('lte::drop_relation_tpl');
        });
        $container->hr(['style' => 'border-top: 0;']);

        Form::$current_model = $old_model_form;
        unset(FormGroup::$construct_modify['build_relation']);

        $hr = $this->hr();
        $row = $hr->row();
        $row->col()->textRight()->button_group(function (ButtonGroup $group) {
            $group->success(['fas fa-plus', __('lte.add')])
                ->on_click('lte::add_relation_tpl',
                    $this->relation_name
                );
        });
        $row->appEnd(Template::create("relation_{$this->relation_name}_template")->appEnd($container));
    }

    /**
     * @param  mixed  ...$params
     * @return mixed
     */
    protected function _call_tpl(...$params)
    {
        $return = call_user_func($this->create_content, ...$params);

        return $return;
    }

    /**
     * @param  mixed  ...$params
     * @return mixed
     */
    protected function _call_empty_tpl(...$params)
    {
        $return = call_user_func($this->on_empty, ...$params);

        return $return;
    }
}
