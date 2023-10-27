<?php

namespace Admin\Traits\ModelRelation;

use Admin\Components\Component;
use Admin\Components\DividerComponent;
use Admin\Components\ModelRelationContainerComponent;
use Illuminate\Database\Eloquent\Model;
use Admin\Components\ButtonsComponent;
use Admin\Components\FormGroupComponent;
use Admin\Components\GridColumnComponent;
use Admin\Components\ModelRelationComponent;
use Admin\Components\ModelRelationContentComponent;
use Admin\Core\ModelSaver;
use Admin\Explanation;

/**
 * @mixin Component
 */
trait ModelRelationBuilderTrait
{
    /**
     * Build relation rows.
     */
    protected function _build(): void
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

            $container = $this->createComponent(
                ModelRelationContainerComponent::class,
                $this->relation_name,
                $item->{$item->getKeyName()}
            );

            $container->view('components.inputs.hidden', [
                'name' => "{$this->relation_name}[".$item->{$item->getKeyName()}."][{$item->getKeyName()}]",
                'value' => $item->{$item->getKeyName()}
            ]);

            $this->last_content = $this->createComponent(
                ModelRelationContentComponent::class,
                $this->relation_name,
                'template_content',
                'template_content'
            );
            $container->appEnd($this->last_content);
            $this->_call_tpl($this->last_content, $item, $this);
            if ($this->last_content->get_test_var('control_group', [$item])) {
                $del = $this->last_content->get_test_var('control_delete', [$item]);

                if ($del || $this->last_content->hasControls()) {


                    if ($del) {
                        $buttonsDel = $this->createComponent(ButtonsComponent::class)
                            ->addCLass('control_relation');
                        $buttonsDel->danger(['fas fa-trash', __('admin.delete')])
                            ->on_click('admin::drop_relation', [
                                admin_view('components.inputs.hidden', [
                                    'classes' => ['delete_field'],
                                    'name' => "{$this->relation_name}[".$item->{$item->getKeyName()}.']['.ModelSaver::DELETE_FIELD.']',
                                    'value' => $item->{$item->getKeyName()}
                                ])->render(),
                            ]);
                        $container->setButtons($buttonsDel);
                    }

                    if ($this->last_content->get_test_var('control_restore') && $del) {
                        $buttonsRestore = $this->createComponent(ButtonsComponent::class)
                            ->addCLass('return_relation')->hide();
                        $text_d = $this->last_content->get_test_var('control_restore_text');
                        $s = $text_d ?: (strtoupper($item->getKeyName()).': '.$item->{$item->getKeyName()});
                        $text = __('admin.restore_subject', ['subject' => $s]);
                        $buttonsRestore->secondary([
                            'fas fa-redo',
                            tag_replace($text, $item),
                        ])->on_click('admin::return_relation');
                        $container->setButtons($buttonsRestore);
                    }
                }
            }

            $this->appEnd($container);
        }

        if (!$datas->count() && $this->on_empty) {
            $container = $this->createComponent(
                ModelRelationContainerComponent::class,
                $this->relation_name,
                'empty',
                'template_empty_container'
            );

            $this->last_content = $this->createComponent(
                ModelRelationContentComponent::class,
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

        $this->template_area("relation_{$this->relation_name}_template");

        $this->_btn();

        ModelRelationComponent::$fm = $this->fm_old;
    }

    /**
     * @param  mixed  ...$params
     * @return void
     */
    protected function _call_tpl(...$params): void
    {
        /**
         * Required Force.
         */
        $this->last_content?->explainForce(Explanation::new($this->innerDelegates));
    }

    /**
     * Build relation template maker button.
     * @return string
     */
    protected function _btn(): string
    {
        $old_model_form = $this->page->model();

        FormGroupComponent::$construct_modify['build_relation'] = function (FormGroupComponent $group) {
            $m = [];
            preg_match('/([a-zA-Z\-_]+)(\[.*])?/', $group->get_name(), $m);
            $group->set_name("{$this->relation_name}[{__id__}][{$m[1]}]".($m[2] ?? ''));
            $group->force_set_id("{__id__}_{$this->relation_name}_{$group->get_id()}");
        };

        $this->page->model(new ($this->page->model()));

        $container = $this->createComponent(
            ModelRelationContainerComponent::class,
            $this->relation_name,
            'template_container'
        );

        $this->last_content = $this->createComponent(
            ModelRelationContentComponent::class,
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
        $buttons = $this->createComponent(ButtonsComponent::class);
        $buttons->warning(['fas fa-minus', __('admin.remove')])->on_click('admin::drop_relation_tpl');
        $container->setButtons($buttons);

        $this->page->model($old_model_form);
        unset(FormGroupComponent::$construct_modify['build_relation']);

        $this->buttons = $this->createComponent(ButtonsComponent::class)
            ->success(['fas fa-plus', __('admin.add')])
            ->on_click(
                'admin::add_relation_tpl',
                $this->relation_name
            );

        $row = $this->row();

        $row->template("relation_{$this->relation_name}_template")
            ->appEnd($container);
    }

    /**
     * @param  mixed  ...$params
     * @return mixed
     */
    protected function _call_empty_tpl(...$params): mixed
    {
        $return = call_user_func($this->on_empty, ...$params);

        return $return;
    }
}
