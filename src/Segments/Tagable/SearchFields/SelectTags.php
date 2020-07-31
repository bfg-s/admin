<?php

namespace Lar\LteAdmin\Segments\Tagable\SearchFields;

/**
 * Class SelectTags
 * @package Lar\LteAdmin\Segments\Tagable\SearchFields
 */
class SelectTags extends \Lar\LteAdmin\Segments\Tagable\Fields\SelectTags
{
    /**
     * @var string
     */
    static $condition = "in";

    /**
     * After construct event
     */
    protected function after_construct()
    {
        $this->nullable();
    }
}