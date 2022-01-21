<?php

namespace Lar\LteAdmin\Components\Fields;

use Lar\LteAdmin\Components\FormGroupComponent;

class CodeMirrorField extends FormGroupComponent
{
    /**
     * @var null
     */
    protected $icon = null;

    /**
     * @var string
     */
    protected $mode = 'html';

    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return \Lar\Layout\Tags\TEXTAREA::create([
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->title,
        ], ...$this->params)
            ->text(e($this->value))
            ->setRules($this->rules)
            ->setDatas($this->data)
            ->on_load("codemirror::{$this->mode}");
    }

    /**
     * @param  string  $mode
     * @return $this
     */
    public function mode(string $mode)
    {
        $this->mode = $mode;

        return $this;
    }
}
