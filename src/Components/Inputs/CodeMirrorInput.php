<?php

namespace Admin\Components\Inputs;

use Admin\Components\FormGroupComponent;
use Illuminate\View\View;

class CodeMirrorInput extends FormGroupComponent
{
    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var string
     */
    protected string $mode = 'html';

    /**
     * @return View
     */
    public function field(): View
    {
        return admin_view('components.inputs.textarea', [
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->title,
            'value' => $this->value,
            'rules' => $this->rules,
            'datas' => array_merge($this->data, ['load' => "codemirror::{$this->mode}"]),
            'has_bug' => $this->has_bug,
            'form_control' => true,
            'attributes' => [],
        ]);
    }

    /**
     * @param  string  $mode
     * @return $this
     */
    public function mode(string $mode): static
    {
        $this->mode = $mode;

        return $this;
    }
}
