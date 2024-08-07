<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Illuminate\View\View;

/**
 * Input admin panel for a slider with which you can select data from the range.
 */
class SliderInput extends InputGroupComponent
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected string|null $icon = null;

    /**
     * Minimum value for the slider.
     *
     * @var int
     */
    protected int $min = 1;

    /**
     * Maximum value for the slider.
     *
     * @var int
     */
    protected int $max = 100;

    /**
     * Step of adding a value for the slider.
     *
     * @var int
     */
    protected int $step = 1;

    /**
     * Disabled slider input.
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
        return admin_view('components.inputs.slider', [
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->placeholder ?: $this->title,
            'value' => $this->value,
            'rules' => $this->rules,
            'datas' => [
                'load' => 'slider',
                'slider-min' => $this->min,
                'slider-max' => $this->max,
                'slider-step' => $this->step,
                'slider-orientation' => 'horizontal',
                'slider-selection' => 'before',
                'slider-tooltip' => 'show',
                'slider-value' => str_contains($this->value, ',') ? "[$this->value]" : $this->value,
            ],
            'has_bug' => $this->has_bug,
            'attributes' => $this->attributes,
        ]);
    }

    /**
     * Set the minimum value for the input slider.
     *
     * @param  int  $value
     * @param  string|null  $message
     * @return $this
     */
    public function min(int $value, string $message = null): static
    {
        $this->min = $value;

        parent::min($value, $message);

        return $this;
    }

    /**
     * Set the maximum value for the input slider.
     *
     * @param  int  $value
     * @param  string|null  $message
     * @return $this
     */
    public function max(int $value, string $message = null): static
    {
        $this->max = $value;

        parent::max($value, $message);

        return $this;
    }

    /**
     * Set step to set the value for the input slider.
     *
     * @param  int  $value
     * @return $this
     */
    public function step(int $value): static
    {
        $this->step = $value;

        return $this;
    }

    /**
     * Disable slider input.
     *
     * @return $this
     */
    public function disabled(): static
    {
        $this->disabled = true;

        return $this;
    }
}
