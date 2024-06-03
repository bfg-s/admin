<?php

declare(strict_types=1);

namespace Admin\Components;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Body component of a modal window.
 */
class ModalBodyComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'modal-body';

    /**
     * Create and add a form in the body of the modal window.
     *
     * @param ...$delegates
     * @return FormComponent
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function form(...$delegates): FormComponent
    {
        $form = $this->createComponent(FormComponent::class, ...$delegates);

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
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
