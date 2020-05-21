<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\Layout\Abstracts\Component;
use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Input
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class MDEditor extends FormGroup
{
    /**
     * @var null
     */
    protected $icon = null;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'md::simple'
    ];

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
        return \Lar\Layout\Tags\DIV::create([
            'id' => $id,
            'data-name' => $name,
            'data-placeholder' => $title,
            'm-0'
        ], ...$this->params)->when(function (\Lar\Layout\Tags\DIV $input) use ($value) {
            $this->makeValue($input, $value ?? $this->value);
        })->setRules($this->rules)
            ->setDatas($this->data)
            ->addClassIf($has_bug, 'is-invalid');
    }

    /**
     * @param  Component  $input
     * @param  mixed|null  $value
     */
    protected function makeValue(Component $input, $value = null)
    {
        if ($value !== null) {

            $input->text($value);
        }
    }
}