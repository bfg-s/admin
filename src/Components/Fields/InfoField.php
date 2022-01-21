<?php

namespace Lar\LteAdmin\Components\Fields;

use Lar\LteAdmin\Components\FormGroupComponent;

class InfoField extends FormGroupComponent
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-quote-right';

    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return \Lar\Layout\Tags\INPUT::create([
            'type' => 'text',
            'id' => $this->field_id,
            'disabled' => 'true',
        ], ...$this->params)
            ->setValue($this->value)
            ->setDatas($this->data)
            ->addClass('form-control');
    }
}
