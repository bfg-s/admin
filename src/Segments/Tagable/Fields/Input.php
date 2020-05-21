<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Input
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Input extends FormGroup
{
    /**
     * @var string
     */
    protected $type = "text";

    /**
     * @var bool
     */
    protected $form_control = true;

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
        return \Lar\Layout\Tags\INPUT::create([
            'type' => $this->type,
            'id' => $id,
            'name' => $name,
            'placeholder' => $title
        ], ...$this->params)->when(function (\Lar\Layout\Tags\INPUT $input) use ($value) {
            $this->makeValue($input, $value ?? $this->value);
        })->setRules($this->rules)
            ->setDatas($this->data)
            ->addClassIf($has_bug, 'is-invalid')
            ->addClassIf($this->form_control, 'form-control');
    }

    /**
     * @param  \Lar\Layout\Tags\INPUT  $input
     * @param  null  $value
     */
    protected function makeValue(\Lar\Layout\Tags\INPUT $input, $value = null)
    {
        $input->setValueIf($value !== null, $value);
    }
}