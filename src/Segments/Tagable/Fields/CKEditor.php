<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\Layout\Abstracts\Component;
use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Input
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class CKEditor extends FormGroup
{
    /**
     * @var null
     */
    protected $icon = null;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'ckeditor'
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
        return \Lar\Layout\Tags\TEXTAREA::create([
            'id' => $id,
            'name' => $name,
            'placeholder' => $title
        ], ...$this->params)->when(function (\Lar\Layout\Tags\TEXTAREA $input) use ($value) {
            $this->makeValue($input, $value ?? $this->value);
        })->setRules($this->rules)
            ->addClassIf($has_bug, 'is-invalid')
            ->setDatas($this->data);
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

    /**
     * @param  string  $toolbar
     * @return $this
     */
    public function toolbar(string $toolbar)
    {
        $this->data['toolbar'] = $toolbar;

        return $this;
    }
}