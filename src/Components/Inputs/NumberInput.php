<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

class NumberInput extends Input
{
    /**
     * @var string
     */
    protected $type = 'number';

    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var int
     */
    protected $value = 0;

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'number',
        'center' => 'false',
    ];

    /**
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
     * @return $this
     */
    public function center(): static
    {
        $this->data['center'] = 'true';

        return $this;
    }
}
