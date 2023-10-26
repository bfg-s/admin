<?php

namespace Admin\Components\Inputs;

class RatingInput extends Input
{
    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var bool
     */
    protected $form_control = false;

    /**
     * @var array
     */
    protected array $data = [
        'load' => 'rating',
        'animate' => 'true',
        'step' => '1',
        'show-clear' => 'false',
        'show-caption' => 'false',
        'size' => 'sm',
    ];

    /**
     * @var int
     */
    protected mixed $default = 0;

    /**
     * @param  int  $value
     * @param  string|null  $message
     * @return static
     */
    public function min(int $value, string $message = null): static
    {
        $this->data['min'] = $value;

        if ($value == 0) {
            $this->data['show-clear'] = 'true';
        }

        return parent::min($value);
    }

    /**
     * @param  int  $value
     * @param  string|null  $message
     * @return static
     */
    public function max(int $value, string $message = null): static
    {
        $this->data['max'] = $value;

        return parent::max($value);
    }

    /**
     * @param  int|float  $step
     * @return $this
     */
    public function step($step): static
    {
        $this->data['step'] = $step;

        return $this;
    }

    /**
     * @return $this
     */
    public function sizeXl(): static
    {
        $this->data['size'] = 'xl';

        return $this;
    }

    /**
     * @return $this
     */
    public function sizeLg(): static
    {
        $this->data['size'] = 'lg';

        return $this;
    }

    /**
     * @return $this
     */
    public function sizeMd(): static
    {
        $this->data['size'] = 'md';

        return $this;
    }

    /**
     * @return $this
     */
    public function sizeSm(): static
    {
        $this->data['size'] = 'sm';

        return $this;
    }

    /**
     * @return $this
     */
    public function sizeXs(): static
    {
        $this->data['size'] = 'xs';

        return $this;
    }

    /**
     * @return $this
     */
    public function readonly(): static
    {
        $this->data['readonly'] = 'true';

        return $this;
    }

    /**
     * @return $this
     */
    public function disabled(): static
    {
        $this->data['disabled'] = 'disabled';

        return parent::disabled();
    }

    /**
     * @param  int  $stars
     * @return RatingInput
     */
    public function stars(int $stars): static
    {
        $this->data['stars'] = $stars;

        return $this;
    }

    /**
     * @return $this
     */
    public function showCaption(): static
    {
        $this->data['show-caption'] = 'true';

        return $this;
    }
}
