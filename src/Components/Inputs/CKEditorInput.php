<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\FormGroupComponent;
use Illuminate\View\View;

class CKEditorInput extends FormGroupComponent
{
    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'ckeditor',
    ];

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
            'form_control' => true,
            'attributes' => [],
        ]);
    }

    /**
     * @param  string  $toolbar
     * @return $this
     */
    public function toolbar(string $toolbar): static
    {
        $this->data['toolbar'] = $toolbar;

        return $this;
    }
}
