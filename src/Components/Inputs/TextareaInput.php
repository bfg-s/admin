<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Illuminate\View\View;

/**
 * Input admin panel for entering large text.
 */
class TextareaInput extends InputGroupComponent
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * Added or not a form control class for input.
     *
     * @var bool
     */
    protected bool $form_control = true;

    /**
     * Number of rows for the text input field.
     *
     * @var int|null
     */
    protected ?int $rows = null;

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
            'placeholder' => $this->placeholder ?: $this->title,
            'value' => $this->value,
            'rules' => $this->rules,
            'datas' => $this->data,
            'has_bug' => $this->has_bug,
            'form_control' => $this->form_control,
            'attributes' => [],
            'rows' => $this->rows,
        ]);
    }

    /**
     * Set the number of lines for the text input field.
     *
     * @param  int  $rows
     * @return $this
     */
    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }
}
