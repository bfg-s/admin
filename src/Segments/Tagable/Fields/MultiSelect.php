<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class Select2
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class MultiSelect extends Select
{

    /**
     * @var array[]
     */
    protected $params = [
        ['multiple' => 'multiple']
    ];
}