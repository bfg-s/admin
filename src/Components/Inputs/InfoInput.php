<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Illuminate\View\View;

/**
 * Input the admin panel to display the field as information.
 */
class InfoInput extends InputGroupComponent
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-quote-right';

    /**
     * Check if the component is ignore for API contents.
     *
     * @var bool
     */
    public bool $ignoreForApi = true;

    /**
     * Method for creating an input field.
     *
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
