<?php

namespace LteAdmin\Traits\ModelRelation;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Components\Template;
use Lar\Layout\Components\TemplateArea;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\INPUT;
use LteAdmin\Components\ButtonsComponent;
use LteAdmin\Components\FormGroupComponent;
use LteAdmin\Components\GridColumnComponent;
use LteAdmin\Components\ModelRelationComponent;
use LteAdmin\Components\ModelRelationContentComponent;
use LteAdmin\Core\ModelSaver;
use LteAdmin\Explanation;

/**
 * @mixin Component
 */
trait ModelRelationBuilderTrait
{
    /**
     * Build relation rows.
     */
    protected function _build()
    {
        $old_model_form = $this->page->model();

        FormGroupComponent::$construct_modify['build_relation'] = function (FormGroupComponent $group, Model $model) {
            $k = $model->{$model->getKeyName()};
            $n = $group->get_name();
            $m = [];
            preg_match('/([a-zA-Z\-\_]+)(\[.*\])?/', $n, $m);
            $group->set_name("{$this->relation_name}[{$k}][{$m[1]}]".($m[2] ?? ''));
            $group->set_id("{$this->relation_name}_{$group->get_id()}_{$k}");
        };

        $datas = $this->relation->get();

        /** @var Model $item */
        foreach ($datas as $item) {
            $this->page->model($item);
            $container = ModelRelationContentComponent::create($this->relation_name, $item->{$item->getKeyName()});
            $container->appEnd(
                INPUT::create()->setType('hidden')
                    ->setName("{$this->relation_name}[".$item->{$item->getKeyName()}."][{$item->getKeyName()}]")
                    ->setValue($item->{$item->getKeyName()})
            );
            $this->last_content = ModelRelationContentComponent::create(
                $this->relation_name,
                'template_content',
                'template_content'
            );
            $container->appEnd($this->last_content);
            $this->_call_tpl($this->last_content, $item, $this);
            if ($this->last_content->get_test_var('control_group', [$item])) {
                $del = $this->last_content->get_test_var('control_delete', [$item]);

                if ($del || $this->last_content->hasControls()) {
                    $container->column()->textRight()->m0()->p0()->when(function (GridColumnComponent $col) use (
                        $item,
                        $del
                    ) {
                        $col->buttons()->when(function (ButtonsComponent $group) use ($item, $del) {
                            $this->last_content->callControls($group, $item);

                            if ($del) {
                                $group->danger(['fas fa-trash', __('lte.delete')])
                                    ->on_click('lte::drop_relation', [
                                        INPUT::create()->setType('hidden')->addClass('delete_field')
                                            ->setName("{$this->relation_name}[".$item->{$item->getKeyName()}.']['.ModelSaver::DELETE_FIELD.']')
                                            ->setValue($item->{$item->getKeyName()})->render(),
                                    ]);
                            }
                        })->addCLass('control_relation');

                        if ($this->last_content->get_test_var('control_restore') && $del) {
                            $col->divider(null, null, function (DIV $div) use ($item) {
                                $div->buttons()->when(function (ButtonsComponent $group) use ($item) {
                                    $text_d = $this->last_content->get_test_var('control_restore_text');
                                    $s = $text_d ? $text_d : (strtoupper($item->getKeyName()).': '.$item->{$item->getKeyName()});
                                    $text = __('lte.restore_subject', ['subject' => $s]);
                                    $group->secondary([
                                        'fas fa-redo',
                                        tag_replace($text, $item),
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
            $container = ModelRelationContentComponent::create(
                $this->relation_name,
                'empty',
                'template_empty_container'
            );
            $this->last_content = ModelRelationContentComponent::create(
                $this->relation_name,
                'template_empty_content',
                'template_empty_content'
            );
            $this->_call_empty_tpl($this->last_content, $this->relation->getQuery()->getModel(), $this);
            $container->appEnd($this->last_content);
            $this->appEnd($container);
        }

        $this->page->model($old_model_form);

        unset(FormGroupComponent::$construct_modify['build_relation']);

        $this->appEnd(TemplateArea::create("relation_{$this->relation_name}_template"));

        $this->_btn();

        $this->callRenderEvents();

        ModelRelationComponent::$fm = $this->fm_old;
    }

    /**
     * @param  mixed  ...$params
     * @return mixed
     */
    protected function _call_tpl(...$params)
    {
        /**
         * Required Force.
         */
        $this->last_content?->explainForce(Explanation::new($this->innerDelegates));
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

    /**
     * Build relation template maker button.
     * @return string
     */
    protected function _btn()
    {
        $old_model_form = $this->page->model();

        FormGroupComponent::$construct_modify['build_relation'] = function (FormGroupComponent $group) {
            $m = [];
            preg_match('/([a-zA-Z\-\_]+)(\[.*\])?/', $group->get_name(), $m);
            $group->set_name("{$this->relation_name}[{__id__}][{$m[1]}]".($m[2] ?? ''));
            $group->set_id("{__id__}_{$this->relation_name}_{$group->get_id()}");
        };

        $this->page->model(new ($this->page->model()));
        $container = ModelRelationContentComponent::create($this->relation_name, 'template_container');
        $this->last_content = ModelRelationContentComponent::create(
            $this->relation_name,
            'template_content',
            'template_content'
        );
        $container->appEnd($this->last_content);
        $this->page->model($this->relation->getQuery()->getModel());
        $this->_call_tpl($this->last_content, $this->relation->getQuery()->getModel(), $this);
        $this->page->model(new ($this->page->model()));
        if (!$this->last_content->get_test_var('control_create')) {
            return '';
        }
        $container->column()->textRight()->p0()->buttons()->when(static function (ButtonsComponent $group) {
            $group->setStyle('margin-left: 0!important;');
            $group->warning(['fas fa-minus', __('lte.remove')])->on_click('lte::drop_relation_tpl');
        });
        $container->hr(['style' => 'border-top: 0;']);

        $this->page->model($old_model_form);
        unset(FormGroupComponent::$construct_modify['build_relation']);

        $hr = $this->hr();
        $row = $hr->row();
        $row->column()->textRight()->buttons()->when(function (ButtonsComponent $group) {
            $group->success(['fas fa-plus', __('lte.add')])
                ->on_click(
                    'lte::add_relation_tpl',
                    $this->relation_name
                );
        });
        $row->appEnd(Template::create("relation_{$this->relation_name}_template")->appEnd($container));
    }
}
