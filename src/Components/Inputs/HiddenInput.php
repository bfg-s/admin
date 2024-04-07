<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\FormGroupComponent;
use Illuminate\View\View;

class HiddenInput extends FormGroupComponent
{
    /**
     * @var bool
     */
    protected bool $vertical = true;

    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var bool
     */
    protected $only_input = true;

    /**
     * @var bool
     */
    protected bool $disabled = false;

    /**
     * @return View
     */
    public function field(): View
    {
        return admin_view('components.inputs.hidden', [
            'name' => $this->name,
            'value' => $this->value,
            'id' => $this->field_id,
            'disabled' => $this->disabled,
        ]);
    }

    /**
     * @return $this
     */
    public function disabled(): static
    {
        $this->disabled = true;

        return $this;
    }
}
