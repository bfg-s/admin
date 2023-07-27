<?php

namespace Admin\Components\Fields;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\INPUT;
use Admin\Components\FormGroupComponent;

class SliderField extends FormGroupComponent
{
    /**
     * @var string
     */
    protected $type = 'text';

    /**
     * @var null
     */
    protected $icon = null;

    protected $min = 1;
    protected $max = 100;
    protected $step = 1;

    /**
     * @return Component|INPUT|mixed
     */
    public function field()
    {
        return INPUT::create([
            'type' => $this->type,
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->title,
            'class' => 'slider form-control',
            'data-slider-min' => $this->min,
            'data-slider-max' => $this->max,
            'data-slider-step' => $this->step,
            'data-slider-orientation' => 'horizontal',
            'data-slider-selection' => 'before',
            'data-slider-tooltip' => 'show',
            'data-slider-value' => $this->value,
        ], ...$this->params)
            ->setValue($this->value)
            ->setDatas([
                'load' => 'slider',
            ]);
    }

    /**
     * @param  int  $value
     * @param  string|null  $message
     * @return $this|SliderField
     */
    public function min(int $value, string $message = null)
    {
        $this->min = $value;

        parent::min($value, $message);

        return $this;
    }

    /**
     * @param  int  $value
     * @param  string|null  $message
     * @return $this|SliderField
     */
    public function max(int $value, string $message = null)
    {
        $this->max = $value;

        parent::max($value, $message);

        return $this;
    }

    /**
     * @param  int  $value
     * @return $this
     */
    public function step(int $value)
    {
        $this->step = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function disabled()
    {
        $this->params[] = ['disabled' => 'true'];

        return $this;
    }
}
