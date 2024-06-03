<?php

declare(strict_types=1);

namespace Admin\Traits;

/**
 * Trade with frontend validation form rules.
 */
trait RulesFrontTrait
{
    /**
     * Validation rules.
     *
     * @var array
     */
    protected array $rules = [];

    /**
     * This field is required.
     *
     * @return $this
     */
    public function is_required(): static
    {
        $this->rules[] = 'required';

        return $this;
    }

    /**
     * Checks the correctness of the e-mail address.
     *
     * @return $this
     */
    public function is_email(): static
    {
        $this->rules[] = 'email';

        return $this;
    }

    /**
     * Checks the correctness of the URL address.
     *
     * @return $this
     */
    public function is_url(): static
    {
        $this->rules[] = 'url';

        return $this;
    }

    /**
     * Checks the date is correct.
     *
     * @return $this
     */
    public function is_date(): static
    {
        $this->rules[] = 'date';

        return $this;
    }

    /**
     * Number check.
     *
     * @return $this
     */
    public function is_number(): static
    {
        $this->rules[] = 'number';

        return $this;
    }

    /**
     * Only numbers.
     *
     * @return $this
     */
    public function is_digits(): static
    {
        $this->rules[] = 'digits';

        return $this;
    }

    /**
     * Equal to something (for example another field equalTo: "#pswd").
     *
     * @param  string  $field
     * @return $this
     */
    public function is_equal_to(string $field): static
    {
        $this->rules['equalTo'] = $field;

        return $this;
    }

    /**
     * Maximum number of characters.
     *
     * @param  int  $max
     * @return $this
     */
    public function is_max_length(int $max): static
    {
        $this->rules['maxlength'] = $max;

        return $this;
    }

    /**
     * Minimum number of characters.
     *
     * @param  int  $min
     * @return $this
     */
    public function is_min_length(int $min): static
    {
        $this->rules['minlength'] = $min;

        return $this;
    }

    /**
     * Number of characters from how many to how many (rangelength: [2, 5]).
     *
     * @param  int  $min
     * @param  int  $max
     * @return $this
     */
    public function is_range_length(int $min, int $max): static
    {
        $this->rules['rangelength'] = "[{$min},{$max}]";

        return $this;
    }

    /**
     * The number must be in the range from and to (range: [2, 12]).
     *
     * @param  int  $min
     * @param  int  $max
     * @return $this
     */
    public function is_range(int $min, int $max): static
    {
        $this->rules['range'] = "[{$min},{$max}]";

        return $this;
    }

    /**
     * The maximum value of the number.
     *
     * @param  int  $max
     * @return $this
     */
    public function is_max(int $max): static
    {
        $this->rules['max'] = $max;

        return $this;
    }

    /**
     * Minimum number value.
     *
     * @param  int  $min
     * @return $this
     */
    public function is_min(int $min): static
    {
        $this->rules['min'] = $min;

        return $this;
    }
}
