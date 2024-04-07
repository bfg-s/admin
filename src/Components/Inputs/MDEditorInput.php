<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\FormGroupComponent;
use Illuminate\View\View;

class MDEditorInput extends FormGroupComponent
{
    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'md::simple',
    ];

    /**
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
