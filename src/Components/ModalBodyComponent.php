<?php

namespace Admin\Components;

class ModalBodyComponent extends Component
{
    /**
     * @var string[]
     */
    protected $props = [
        'modal-body',
    ];

    public function form(...$delegates)
    {
        $form = FormComponent::create(...$delegates);

        $form->vertical()->attr('target');
        if (request()->has('_modal_id')) {
            $form->onSubmit("event.preventDefault();'modal:submit'.exec('".request()->get('_modal_id')."');return false;");
        } else {
            $form->onSubmit("event.preventDefault();return false;");
        }

        $this->appEnd($form);

        return $form;
    }

    protected function mount()
    {
        // TODO: Implement mount() method.
    }
}
