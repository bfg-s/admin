<?php

namespace Lar\LteAdmin\Segments\Tagable\SearchFields;

/**
 * Class Select
 * @package Lar\LteAdmin\Segments\Tagable\SearchFields
 */
class Select extends \Lar\LteAdmin\Segments\Tagable\Fields\Select
{
    /**
     * @var string
     */
    static $condition = "=";

    /**
     * After construct event
     */
    protected function after_construct()
    {
        $this->nullable();
    }
}