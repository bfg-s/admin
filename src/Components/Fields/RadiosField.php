<?php

namespace Lar\LteAdmin\Components\Fields;

use Lar\LteAdmin\Components\Cores\RadioFieldCore;

class RadiosField extends ChecksField
{
    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return RadioFieldCore::create($this->options)
            ->name($this->name)
            ->id($this->field_id)
            ->value($this->value)
            ->setRules($this->rules)
            ->setDatas($this->data);
    }
}
