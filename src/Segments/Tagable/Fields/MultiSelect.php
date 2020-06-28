<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class MultiSelect
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