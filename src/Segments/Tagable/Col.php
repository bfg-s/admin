<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 */
class Col extends DIV {

    /**
     * @var string
     */
    protected $class = 'col-md';

    /**
     * Col constructor.
     * @param int|\Closure $num
     * @param  mixed  ...$params
     */
    public function __construct($num = null, ...$params)
    {
        parent::__construct();

        if (is_numeric($num)) {

            $this->class .= "-{$num}";

        } else if ($num) {

            $params[] = $num;
        }

        $this->when($params);

        $this->addClass($this->class);
    }
}