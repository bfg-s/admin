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
     * The modal window body to be without padding.
     *
     * @var bool
     */
    protected bool $withOutPadding = false;

    /**
     * Set the modal window body to be without padding.
     *
     * @return $this
     */
    public function withOutPadding(): static
    {
        $this->withOutPadding = true;

        return $this;
    }

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
        if ($this->parent->getModalName()) {
            $form->setOnSubmit("event.preventDefault();exec('modal:submit', '".$this->parent->getModalName()."');return false;");
        } else {
            $form->setOnSubmit("event.preventDefault();return false;");
        }

        $this->appEnd($form);

        return $form;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return bool[]
     */
    protected function viewData(): array
    {
        return [
            'withOutPadding' => $this->withOutPadding,
        ];
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
