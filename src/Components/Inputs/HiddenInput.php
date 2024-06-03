<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Illuminate\View\View;

/**
 * Hidden admin panel input.
 */
class HiddenInput extends InputGroupComponent
{
    /**
     * Vertical display of input and label.
     *
     * @var bool
     */
    protected bool $vertical = true;

    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * Display only the input without a label.
     *
     * @var bool
     */
    protected bool $only_input = true;

    /**
     * Admin panel input is disabled
     *
     * @var bool
     */
    protected bool $disabled = false;

    /**
     * Method for creating an input field.
     *
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
     * Make the admin panel input disabled.
     *
     * @return $this
     */
    public function disabled(): static
    {
        $this->disabled = true;

        return $this;
    }
}
