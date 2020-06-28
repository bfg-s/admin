<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class Number
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Number extends Input
{
    /**
     * @var string
     */
    protected $type = "number";

    /**
     * @var string
     */
    protected $icon = null;

    /**
     * @var int
     */
    protected $value = 0;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'number',
        'center' => 'false'
    ];

    /**
     * The field under validation must have a minimum value. Strings, numerics,
     * arrays, and files are evaluated in the same fashion as the size rule.
     *
     * @param  int  $value
     * @param  string|null  $message
     * @return $this
     */
    public function min(int $value, string $message = null)
    {
        $this->params[]['min'] = $value;
        return  $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * The field under validation must be less than or equal to a maximum value.
     * Strings, numerics, arrays, and files are evaluated in the same fashion as
     * the size rule.
     *
     * @param  int  $value
     * @param  string|null  $message
     * @return $this
     */
    public function max(int $value, string $message = null)
    {
        $this->params[]['max'] = $value;
        return  $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * @return $this
     */
    public function center()
    {
        $this->data['center'] = 'true';

        return $this;
    }
}