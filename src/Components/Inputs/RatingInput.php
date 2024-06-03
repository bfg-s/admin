<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input admin panel for rating.
 */
class RatingInput extends Input
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected string|null $icon = null;

    /**
     * Added or not a form control class for input.
     *
     * @var bool
     */
    protected bool $form_control = true;

    /**
     * Settable date attributes.
     *
     * @var array
     */
    protected array $data = [
        'load' => '{"rating": []}',
        'animate' => 'true',
        'step' => '1',
        'show-clear' => 'false',
        'show-caption' => 'false',
        'size' => 'sm',
    ];

    /**
     * Default input value if no value is set.
     *
     * @var mixed
     */
    protected mixed $default = 0;

    /**
     * Set the minimum rating value.
     *
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
     * Set the maximum rating value.
     *
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
     * Set the rating value step.
     *
     * @param  float|int  $step
     * @return $this
     */
    public function step(float|int $step): static
    {
        $this->data['step'] = $step;

        return $this;
    }

    /**
     * Set rating size to "xl".
     *
     * @return $this
     */
    public function sizeXl(): static
    {
        $this->data['size'] = 'xl';

        return $this;
    }

    /**
     * Set the rating size to "lg".
     *
     * @return $this
     */
    public function sizeLg(): static
    {
        $this->data['size'] = 'lg';

        return $this;
    }

    /**
     * Set the rating size to "md".
     *
     * @return $this
     */
    public function sizeMd(): static
    {
        $this->data['size'] = 'md';

        return $this;
    }

    /**
     * Set the rating size to "sm".
     *
     * @return $this
     */
    public function sizeSm(): static
    {
        $this->data['size'] = 'sm';

        return $this;
    }

    /**
     * Set the rating size to "xs".
     *
     * @return $this
     */
    public function sizeXs(): static
    {
        $this->data['size'] = 'xs';

        return $this;
    }

    /**
     * Set the rating value to read-only.
     *
     * @return $this
     */
    public function readonly(): static
    {
        $this->data['readonly'] = 'true';

        return $this;
    }

    /**
     * Disable rating input.
     *
     * @return $this
     */
    public function disabled(): static
    {
        $this->data['disabled'] = 'disabled';

        return parent::disabled();
    }

    /**
     * Set the number of rating stars.
     *
     * @param  int  $stars
     * @return RatingInput
     */
    public function stars(int $stars): static
    {
        $this->data['stars'] = $stars;

        return $this;
    }

    /**
     * Display caption.
     *
     * @return $this
     */
    public function showCaption(): static
    {
        $this->data['show-caption'] = 'true';

        return $this;
    }
}
