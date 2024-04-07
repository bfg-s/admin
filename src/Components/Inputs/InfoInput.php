<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\FormGroupComponent;
use Illuminate\View\View;

class InfoInput extends FormGroupComponent
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-quote-right';

    /**
     * @return View
     */
    public function field(): View
    {
        return admin_view('components.inputs.info', [
            'name' => $this->name,
            'value' => $this->value,
            'datas' => $this->data,
            'id' => $this->field_id,
        ]);
    }
}
