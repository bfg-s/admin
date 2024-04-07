<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\FormGroupComponent;
use Illuminate\View\View;

class SliderInput extends FormGroupComponent
{
    /**
     * @var string
     */
    protected string $type = 'text';

    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var int
     */
    protected int $min = 1;

    /**
     * @var int
     */
    protected int $max = 100;

    /**
     * @var int
     */
    protected int $step = 1;

    /**
     * @var bool
     */
    protected bool $disabled = false;

    /**
     * @return View
     */
    public function field(): View
    {
        return admin_view('components.inputs.slider', [
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->title,
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
                'slider-value' => $this->value,
            ],
            'has_bug' => $this->has_bug,
            'attributes' => $this->attributes,
        ]);
    }

    /**
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
     * @param  int  $value
     * @return $this
     */
    public function step(int $value): static
    {
        $this->step = $value;

        return $this;
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
