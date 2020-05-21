<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Components\HorizontalRadio;
use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Radios
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Radios extends Checks
{
    /**
     * @param  string  $name
     * @param  string  $title
     * @param  string  $id
     * @param  null  $value
     * @param  bool  $has_bug
     * @param  null  $path
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field(string $name, string $title, string $id = '', $value = null, bool $has_bug = false, $path = null)
    {
        return HorizontalRadio::create($this->options)->name($name)->id($id)->value($value)->setRules($this->rules)
            ->setDatas($this->data);
    }
}