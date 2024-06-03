<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Illuminate\View\View;

/**
 * Input admin panel CKEditor.
 */
class CKEditorInput extends InputGroupComponent
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'ckeditor',
    ];

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
            'datas' => $this->data,
            'has_bug' => $this->has_bug,
            'form_control' => true,
            'attributes' => [],
        ]);
    }

    /**
     * Method for installing a custom toolbar.
     *
     * @param  string  $toolbar
     * @return $this
     */
    public function toolbar(string $toolbar): static
    {
        $this->data['toolbar'] = $toolbar;

        return $this;
    }
}
