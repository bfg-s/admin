<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;

class Col extends DIV
{
    /**
     * @var int|string|null
     */
    private $num;

    /**
     * Col constructor.
     * @param  null  $num
     * @param  mixed  ...$params
     */
    public function __construct($num = null, ...$params)
    {
        if (is_numeric($num)) {

            $this->num = $num;
        }

        else if ($num !== null) {

            $params[] = $num;
        }

        parent::__construct();

        $this->when($params);
    }

    /**
     * @param  string  $type
     * @return $this
     */
    public function colType(string $type = '')
    {
        if ($type) $type = '-'.$type;

        if ($this->num) {

            $this->addClass("col{$type}-{$this->num}");
        }

        else {

            $this->addClass("col{$type}");
        }

        return $this;
    }
}