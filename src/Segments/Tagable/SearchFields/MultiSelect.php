<?php

namespace Lar\LteAdmin\Segments\Tagable\SearchFields;

/**
 * Class MultiSelect.
 * @package Lar\LteAdmin\Segments\Tagable\SearchFields
 */
class MultiSelect extends \Lar\LteAdmin\Segments\Tagable\Fields\MultiSelect
{
    /**
     * @var string
     */
    public static $condition = 'in';
}
