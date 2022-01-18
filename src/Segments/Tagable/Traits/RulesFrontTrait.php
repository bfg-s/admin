<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits;

use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Trait FormGroupRulesTrait.
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait RulesFrontTrait
{
    /**
     * Validation rules.
     * @var array
     */
    protected $rules = [];

    /**
     * поле обязательное для заполнения.
     * @return $this|FormGroup
     */
    protected function _front_rule_required()
    {
        $this->rules[] = 'required';

        return $this;
    }

    /**
     * проверяет корректность e-mail адреса.
     * @return $this|FormGroup
     */
    protected function _front_rule_email()
    {
        $this->rules[] = 'email';

        return $this;
    }

    /**
     * проверяет корректность url адреса.
     * @return $this|FormGroup
     */
    protected function _front_rule_url()
    {
        $this->rules[] = 'url';

        return $this;
    }

    /**
     * проверяет корректность даты.
     * @return $this|FormGroup
     */
    protected function _front_rule_date()
    {
        $this->rules[] = 'date';

        return $this;
    }

    /**
     * проверка на число.
     * @return $this|FormGroup
     */
    protected function _front_rule_number()
    {
        $this->rules[] = 'number';

        return $this;
    }

    /**
     * только цифры.
     * @return $this|FormGroup
     */
    protected function _front_rule_digits()
    {
        $this->rules[] = 'digits';

        return $this;
    }

    /**
     * равное чему-то (например другому полю equalTo: "#pswd").
     * @param  string  $field
     * @return $this|FormGroup
     */
    protected function _front_rule_equal_to(string $field)
    {
        $this->rules['equalTo'] = $field;

        return $this;
    }

    /**
     * максимальное кол-во символов.
     * @param  int  $max
     * @return $this|FormGroup
     */
    protected function _front_rule_max_length(int $max)
    {
        $this->rules['maxlength'] = $max;

        return $this;
    }

    /**
     * минимальное кол-во символов.
     * @param  int  $min
     * @return $this|FormGroup
     */
    protected function _front_rule_min_length(int $min)
    {
        $this->rules['minlength'] = $min;

        return $this;
    }

    /**
     * кол-во символов от скольких и до скольких (rangelength: [2, 5]).
     * @param  int  $min
     * @param  int  $max
     * @return $this|FormGroup
     */
    protected function _front_rule_range_length(int $min, int $max)
    {
        $this->rules['rangelength'] = "[{$min},{$max}]";

        return $this;
    }

    /**
     * число должно быть в диапазоне от и до (range: [2, 12]).
     * @param  int  $min
     * @param  int  $max
     * @return $this|FormGroup
     */
    protected function _front_rule_range(int $min, int $max)
    {
        $this->rules['range'] = "[{$min},{$max}]";

        return $this;
    }

    /**
     * максимальное значение числа.
     * @param  int  $max
     * @return $this|FormGroup
     */
    protected function _front_rule_max(int $max)
    {
        $this->rules['max'] = $max;

        return $this;
    }

    /**
     * минимальное значение числа.
     * @param  int  $min
     * @return $this|FormGroup
     */
    protected function _front_rule_min(int $min)
    {
        $this->rules['min'] = $min;

        return $this;
    }
}
