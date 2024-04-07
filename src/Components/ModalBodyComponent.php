<?php

declare(strict_types=1);

namespace Admin\Components;

class ModalBodyComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'modal-body';

    /**
     * @param ...$delegates
     * @return FormComponent
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function form(...$delegates): FormComponent
    {
        $form = FormComponent::create(...$delegates);

        $form->vertical()->attr('target');
        if (request()->has('_modal_id')) {
            $form->setOnSubmit("event.preventDefault();exec('modal:submit', '".request()->get('_modal_id')."');return false;");
        } else {
            $form->setOnSubmit("event.preventDefault();return false;");
        }

        $this->appEnd($form);

        return $form;
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
