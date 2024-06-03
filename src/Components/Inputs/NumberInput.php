<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input admin panel for entering numbers.
 */
class NumberInput extends Input
{
    /**
     * HTML attribute of the input type.
     *
     * @var string
     */
    protected $type = 'number';

    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * Input value.
     *
     * @var int
     */
    protected mixed $value = 0;

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'number',
        'center' => 'false',
    ];

    /**
     * Set the value of the step to add to the value.
     *
     * @param  float|int  $step
     * @return $this
     */
    public function step(float|int $step): static
    {
        $this->attributes['step'] = $step;

        return $this;
    }

    /**
     * The field under validation must have a minimum value. Strings, numerics,
     * arrays, and files are evaluated in the same fashion as the size rule.
     *
     * @param  int|float  $value
     * @param  string|null  $message
     * @return $this
     */
    public function min($value, string $message = null): static
    {
        $this->attributes['min'] = $value;

        return $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * The field under validation must be less than or equal to a maximum value.
     * Strings, numerics, arrays, and files are evaluated in the same fashion as
     * the size rule.
     *
     * @param  int|float  $value
     * @param  string|null  $message
     * @return $this
     */
    public function max($value, string $message = null): static
    {
        $this->attributes['max'] = $value;

        return $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * Center the input value.
     *
     * @return $this
     */
    public function center(): static
    {
        $this->data['center'] = 'true';

        return $this;
    }
}
