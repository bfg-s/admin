<?php

namespace Lar\LteAdmin\Components\Fields;

use Lar\LteAdmin\Components\Cores\Select2TagsFieldCore;

class SelectTagsField extends SelectField
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-tags';

    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return Select2TagsFieldCore::create($this->options, [
            'name' => $this->name,
            'data-placeholder' => $this->title,
            'id' => $this->field_id,
        ], ...$this->params)
            ->setValues($this->value)
            ->makeOptions()
            ->setDatas($this->data)
            ->addClassIf($this->has_bug, 'is-invalid')
            ->addClass($this->class);
    }

    /**
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $options
     * @param  bool  $first_default
     * @return SelectTagsField
     */
    public function options($options, bool $first_default = false)
    {
        return parent::options($options, $this->load_subject ? false : $first_default);
    }
}
