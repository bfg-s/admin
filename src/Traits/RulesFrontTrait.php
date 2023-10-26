<?php

namespace Admin\Traits;

use Admin\Components\FormGroupComponent;

trait RulesFrontTrait
{
    /**
     * Validation rules.
     * @var array
     */
    protected array $rules = [];

    /**
     * поле обязательное для заполнения.
     * @return $this
     */
    public function is_required(): static
    {
        $this->rules[] = 'required';

        return $this;
    }

    /**
     * проверяет корректность e-mail адреса.
     * @return $this
     */
    public function is_email(): static
    {
        $this->rules[] = 'email';

        return $this;
    }

    /**
     * проверяет корректность url адреса.
     * @return $this
     */
    public function is_url(): static
    {
        $this->rules[] = 'url';

        return $this;
    }

    /**
     * проверяет корректность даты.
     * @return $this
     */
    public function is_date(): static
    {
        $this->rules[] = 'date';

        return $this;
    }

    /**
     * проверка на число.
     * @return $this
     */
    public function is_number(): static
    {
        $this->rules[] = 'number';

        return $this;
    }

    /**
     * только цифры.
     * @return $this
     */
    public function is_digits(): static
    {
        $this->rules[] = 'digits';

        return $this;
    }

    /**
     * равное чему-то (например другому полю equalTo: "#pswd").
     * @param  string  $field
     * @return $this
     */
    public function is_equal_to(string $field): static
    {
        $this->rules['equalTo'] = $field;

        return $this;
    }

    /**
     * максимальное кол-во символов.
     * @param  int  $max
     * @return $this
     */
    public function is_max_length(int $max): static
    {
        $this->rules['maxlength'] = $max;

        return $this;
    }

    /**
     * минимальное кол-во символов.
     * @param  int  $min
     * @return $this
     */
    public function is_min_length(int $min): static
    {
        $this->rules['minlength'] = $min;

        return $this;
    }

    /**
     * кол-во символов от скольких и до скольких (rangelength: [2, 5]).
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
     * число должно быть в диапазоне от и до (range: [2, 12]).
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
     * максимальное значение числа.
     * @param  int  $max
     * @return $this
     */
    public function is_max(int $max): static
    {
        $this->rules['max'] = $max;

        return $this;
    }

    /**
     * минимальное значение числа.
     * @param  int  $min
     * @return $this
     */
    public function is_min(int $min): static
    {
        $this->rules['min'] = $min;

        return $this;
    }
}
