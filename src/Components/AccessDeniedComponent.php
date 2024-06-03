<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Represents a component that displays an access denied alert.
 */
class AccessDeniedComponent extends Component
{
    /**
     * The tag element from which the component begins.
     *
     * @var string
     */
    protected string $element = "section";

    /**
     * The CSS class that needs to be applied to the parent element.
     *
     * @var string|null
     */
    protected string|null $class = 'content-header';

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        $this->alert(
            AlertComponent::new()
                ->title('admin.error')
                ->body('admin.access_denied')
                ->dangerType()
                ->icon_exclamation_triangle()
                ->mt3()
                ->w100()
        );
    }
}
