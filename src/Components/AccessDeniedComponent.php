<?php

namespace Admin\Components;

class AccessDeniedComponent extends Component
{
    /**
     * @var string
     */
    protected $element = "section";

    /**
     * @var string
     */
    protected $class = 'content-header';

    /**
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
