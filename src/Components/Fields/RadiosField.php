<?php

namespace Admin\Components\Fields;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\INPUT;
use Admin\Components\Cores\RadioFieldCore;

class RadiosField extends ChecksField
{
    /**
     * @return Component|INPUT|mixed
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
