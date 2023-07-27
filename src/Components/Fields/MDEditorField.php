<?php

namespace Admin\Components\Fields;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\INPUT;
use Admin\Components\FormGroupComponent;

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
     * @return Component|INPUT|mixed
     */
    public function field()
    {
        return DIV::create([
            'id' => $this->field_id,
            'data-name' => $this->name,
            'data-placeholder' => $this->title,
            'style' => 'z-index: 1051',
            'm-0',
        ], ...$this->params)
            ->text(e($this->value))
            ->setRules($this->rules)
            ->setDatas($this->data)
            ->addClassIf($this->has_bug, 'is-invalid');
    }
}
