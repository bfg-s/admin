<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits;

use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Trait FormGroupRulesTrait
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait FormGroupRulesTrait {

    /**
     * Validation rules
     * @var array
     */
    protected $rules = [];

    /**
     * поле обязательное для заполнения
     * @return $this|FormGroup
     */
    public function isRequired()
    {
        $this->rules[] = 'required';

        return $this;
    }

    /**
     * проверяет корректность e-mail адреса
     * @return $this|FormGroup
     */
    public function isEmail()
    {
        $this->rules[] = 'email';

        return $this;
    }

    /**
     * проверяет корректность url адреса
     * @return $this|FormGroup
     */
    public function isUrl()
    {
        $this->rules[] = 'url';

        return $this;
    }

    /**
     * проверяет корректность url адреса
     * @return $this|FormGroup
     */
    public function isDate()
    {
        $this->rules[] = 'date';

        return $this;
    }

    /**
     * проверка на число
     * @return $this|FormGroup
     */
    public function isNumber()
    {
        $this->rules[] = 'number';

        return $this;
    }

    /**
     * только цифры
     * @return $this|FormGroup
     */
    public function isDigits()
    {
        $this->rules[] = 'digits';

        return $this;
    }

    /**
     * равное чему-то (например другому полю equalTo: "#pswd")
     * @param  string  $field
     * @return $this|FormGroup
     */
    public function isEqualTo(string $field)
    {
        $this->rules['equalTo'] = $field;

        return $this;
    }

    /**
     * максимальное кол-во символов
     * @param  int  $max
     * @return $this|FormGroup
     */
    public function isMaxLength(int $max)
    {
        $this->rules['maxlength'] = $max;

        return $this;
    }

    /**
     * минимальное кол-во символов
     * @param  int  $min
     * @return $this|FormGroup
     */
    public function isMinLength(int $min)
    {
        $this->rules['minlength'] = $min;

        return $this;
    }

    /**
     * кол-во символов от скольких и до скольких (rangelength: [2, 5])
     * @param  int  $min
     * @param  int  $max
     * @return $this|FormGroup
     */
    public function isRangeLength(int $min, int $max)
    {
        $this->rules['rangelength'] = "[{$min},{$max}]";

        return $this;
    }

    /**
     * число должно быть в диапазоне от и до (range: [2, 12])
     * @param  int  $min
     * @param  int  $max
     * @return $this|FormGroup
     */
    public function isRange(int $min, int $max)
    {
        $this->rules['range'] = "[{$min},{$max}]";

        return $this;
    }

    /**
     * максимальное значение числа
     * @param  int  $max
     * @return $this|FormGroup
     */
    public function isMax(int $max)
    {
        $this->rules['max'] = $max;

        return $this;
    }

    /**
     * минимальное значение числа
     * @param  int  $min
     * @return $this|FormGroup
     */
    public function isMin(int $min)
    {
        $this->rules['min'] = $min;

        return $this;
    }
}