<?php

namespace Admin\Components\Fields;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\INPUT;
use Admin\Components\FormGroupComponent;

class InfoField extends FormGroupComponent
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-quote-right';

    /**
     * @return Component|INPUT|mixed
     */
    public function field()
    {
        return INPUT::create([
            'type' => 'text',
            'id' => $this->field_id,
            'disabled' => 'true',
        ], ...$this->params)
            ->setValue($this->value)
            ->setDatas($this->data)
            ->addClass('form-control');
    }
}
