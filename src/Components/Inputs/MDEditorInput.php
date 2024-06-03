<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Illuminate\View\View;

/**
 * Input the admin panel to display markdown data.
 */
class MDEditorInput extends InputGroupComponent
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected string|null $icon = null;

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'md::simple',
    ];

    /**
     * Method for creating an input field.
     *
     * @return View
     */
    public function field(): View
    {
        return admin_view('components.inputs.md-editor', [
            'id' => $this->field_id,
            'dataName' => $this->name,
            'placeholder' => $this->title,
            'datas' => $this->data,
            'rules' => $this->rules,
            'value' => $this->value,
        ]);
    }
}
