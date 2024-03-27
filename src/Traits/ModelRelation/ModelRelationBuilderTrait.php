<?php

namespace Admin\Traits\ModelRelation;

use Admin\Components\Component;
use Admin\Components\ModelRelationContainerComponent;
use Illuminate\Database\Eloquent\Model;
use Admin\Components\ButtonsComponent;
use Admin\Components\ModelRelationComponent;
use Admin\Components\ModelRelationContentComponent;
use Admin\Core\ModelSaver;
use Admin\Explanation;
use Throwable;

/**
 * @mixin Component
 */
trait ModelRelationBuilderTrait
{
    /**
     * Build relation rows.
     * @throws Throwable
     */
    protected function buildNestedTemplate(): void
    {
        if (! $this->ordered) {
            $datas = $this->relation->get();
        } else {
            $datas = $this->relation->orderBy($this->ordered)->get();
        }

        $i = 0;

        $preventModel = $this->page->getModel();
        $preventModelThis = $this->model;

        /** @var Model $item */
        foreach ($datas as $item) {

            $this->page->model($item);
            $this->model($item);

            $container = $this->createComponent(
                ModelRelationContainerComponent::class,
                $this->relation_name,
                $item->{$item->getKeyName()}
            )->setOrdered($this->ordered);

            $container->model($item);

            $deepNames = $this->deepNames();

            $nameStart = $this->namesToString($deepNames);

            $container->view('components.inputs.hidden', [
                'name' => "{$nameStart}[{$item->getKeyName()}]",
                'value' => $item->{$item->getKeyName()}
            ]);

            if ($this->ordered) {

                $container->view('components.inputs.hidden', [
                    'name' => "{$nameStart}[{$this->ordered}]",
                    'value' => $item->{$this->ordered} ?: $i,
                    'classes' => ['ordered-field']
                ]);
            }

            $this->last_content = $this->createComponent(
                ModelRelationContentComponent::class,
                $this->relation_name,
                'template_content',
                'template_content'
            );

            $this->applyTemplate();

            $container->appEnd($this->last_content);

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
                                    'name' => "{$nameStart}[".ModelSaver::DELETE_FIELD.']',
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

            $this->appEnd($container->render());

            $i++;
        }

        $this->page->model($preventModel);
        $this->model($preventModelThis);

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

            $this->callEmptyTemplate($this->last_content, $this->relation->getQuery()->getModel(), $this);
            $container->appEnd($this->last_content);
            $this->appEnd($container);
        }

        $this->generateButton();

        ModelRelationComponent::$fm = $this->fm_old;
    }

    /**
     * @param $array
     * @return string
     */
    protected function namesToString($array): string
    {
        if (empty($array)) {
            return '';
        }

        $firstElement = array_shift($array);
        $formattedElements = array_map(function($item) {
            return sprintf('[%s]', $item);
        }, $array);

        return $firstElement . implode('', $formattedElements);
    }

    /**
     * Build relation template maker button.
     * @return void
     */
    protected function generateButton(): void
    {
        ModelRelationComponent::templateMode(true);

        $container = $this->createComponent(
            ModelRelationContainerComponent::class,
            $this->relation_name,
            'template_container'
        )->setOrdered($this->ordered);

        if ($this->ordered) {

            $deepNames = $this->deepNames();

            $nameStart = $this->namesToString($deepNames);

            $container->view('components.inputs.hidden', [
                'name' => "{$nameStart}[{__id__}][{$this->ordered}]",
                'value' => '{__val__}',
                'classes' => ['ordered-field']
            ]);
        }

        $this->last_content = $this->createComponent(
            ModelRelationContentComponent::class,
            $this->relation_name,
            'template_content',
            'template_content'
        );

        $this->applyTemplate();

        $container->appEnd($this->last_content);

        if (!$this->last_content->get_test_var('control_create')) {
            return ;
        }
        $buttons = $this->createComponent(ButtonsComponent::class);
        $buttons->warning(['fas fa-minus', __('admin.remove')])->on_click('admin::drop_relation_tpl');
        $container->setButtons($buttons);

        $this->buttons = $this->createComponent(ButtonsComponent::class)
            ->success(['fas fa-plus', __('admin.add')])
            ->on_click(
                'admin::add_relation_tpl',
                $this->relation_name
            );

        $row = $this->row();

        $row->template("relation_{$this->relation_name}_template")
            ->appEnd($container);


        ModelRelationComponent::templateMode(false);
    }

    /**
     * @return void
     */
    protected function applyTemplate(): void
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
    protected function callEmptyTemplate(...$params): mixed
    {
        return call_user_func($this->on_empty, ...$params);
    }
}
