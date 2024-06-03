<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input admin panel for value switch.
 */
class SwitcherInput extends Input
{
    /**
     * HTML attribute of the input type.
     *
     * @var string
     */
    protected $type = 'checkbox';

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
    protected bool $form_control = false;

    /**
     * Settable date attributes.
     *
     * @var array
     */
    protected array $data = [
        'load' => 'bootstrapSwitch'
    ];

    /**
     * Set a custom switch size.
     *
     * @param  string  $size
     * @return $this
     */
    public function switchSize(string $size): static
    {
        $this->data['size'] = $size;

        return $this;
    }

    /**
     * Set labels for switch states.
     *
     * @param  string|null  $on
     * @param  string|null  $off
     * @param  string|null  $label
     * @return $this
     */
    public function labels(string $on = null, string $off = null, string $label = null): static
    {
        if ($on) {
            $this->data['on-text'] = $on;
        }
        if ($off) {
            $this->data['off-text'] = $off;
        }
        if ($label) {
            $this->data['label-text'] = $label;
        }

        return $this;
    }

    /**
     * Method to override, event before creating the input wrapper.
     *
     * @return void
     */
    protected function on_build(): void
    {
        if (!isset($this->data['on-text'])) {
            $this->data['on-text'] = __('admin.on');
        }

        if (!isset($this->data['off-text'])) {
            $this->data['off-text'] = __('admin.off');
        }
    }

    /**
     * Create a value for the input.
     *
     * @return int
     */
    protected function create_value(): int
    {
        if (parent::create_value()) {

            $this->checked = true;
        }

        return 1;
    }
}
