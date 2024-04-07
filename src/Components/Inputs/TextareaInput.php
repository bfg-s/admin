<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\FormGroupComponent;
use Illuminate\View\View;

class TextareaInput extends FormGroupComponent
{
    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var bool
     */
    protected bool $form_control = true;

    /**
     * @var int|null
     */
    protected ?int $rows = null;

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
            'datas' => $this->data,
            'has_bug' => $this->has_bug,
            'form_control' => $this->form_control,
            'attributes' => [],
            'rows' => $this->rows,
        ]);
    }

    /**
     * @param  int  $rows
     * @return $this
     */
    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }
}
