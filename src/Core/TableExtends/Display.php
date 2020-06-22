<?php

namespace Lar\LteAdmin\Core\TableExtends;

use Lar\Layout\Tags\TD;
use Lar\Layout\Tags\TH;

/**
 * Class Display
 * @package Lar\LteAdmin\Core\TableExtends
 */
class Display {

    /**
     * @param $value
     * @param  TD|null  $td
     * @param  TH|null  $th
     * @return mixed
     */
    public function hide_om_mobile($value, TD $td = null, TH $th = null)
    {
        if ($td) {
            $td->addClass('d-none', 'd-sm-table-cell');
        }
        if ($th) {
            $th->addClass('d-none', 'd-sm-table-cell');
        }

        return $value;
    }
}