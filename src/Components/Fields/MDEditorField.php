<?php

namespace Lar\LteAdmin\Components\Fields;

use Lar\LteAdmin\Components\FormGroupComponent;

class MDEditorField extends FormGroupComponent
{
    /**
     * @var null
     */
    protected $icon = null;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'md::simple',
    ];

    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return \Lar\Layout\Tags\DIV::create([
            'id' => $this->field_id,
            'data-name' => $this->name,
            'data-placeholder' => $this->title,
            'm-0',
        ], ...$this->params)
            ->text(e($this->value))
            ->setRules($this->rules)
            ->setDatas($this->data)
            ->addClassIf($this->has_bug, 'is-invalid');
    }
}
