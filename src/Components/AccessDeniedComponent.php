<?php

namespace Admin\Components;

class AccessDeniedComponent extends Component
{
    protected $element = "section";

    protected $class = 'content-header';

    protected function mount()
    {
        $this->alert(
            AlertComponent::new()
                ->title('admin.error')
                ->body('admin.access_denied')
                ->dangerType()
                ->icon_exclamation_triangle()
                ->mt3()
        );
    }
}
