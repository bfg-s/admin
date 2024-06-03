<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Illuminate\View\View;

/**
 * Input admin panel CodeMirror.
 */
class CodeMirrorInput extends InputGroupComponent
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * CodeMirror mode.
     *
     * @var string
     */
    protected string $mode = 'html';

    /**
     * Method for creating an input field.
     *
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
     * Set your own CodeMirror mode.
     *
     * @param  string  $mode
     * @return $this
     */
    public function mode(string $mode): static
    {
        $this->mode = $mode;

        return $this;
    }
}
