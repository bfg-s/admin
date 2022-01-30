<?php

namespace LteAdmin\Components;

class AccessDeniedComponent extends Component
{
    protected $element = "section";

    protected $class = 'content-header';

    protected function mount()
    {
        $this->alert(
            AlertComponent::new()
                ->title('lte.error')
                ->body('lte.access_denied')
                ->dangerType()
                ->icon_exclamation_triangle()
                ->mt3()
        );
    }
}
