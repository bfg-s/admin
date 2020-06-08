<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\Cores\CoreRadio;

/**
 * Class Radios
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Radios extends Checks
{
    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return CoreRadio::create($this->options)
            ->name($this->name)
            ->id($this->field_id)
            ->value($this->value)
            ->setRules($this->rules)
            ->setDatas($this->data);
    }
}