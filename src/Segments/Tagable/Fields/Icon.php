<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class Icon
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Icon extends Input
{
    /**
     * @var string
     */
    protected $icon = "fas fa-icons";

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  string  $id
     * @param  null  $value
     * @param  bool  $has_bug
     * @param  null  $path
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed|string
     */
    public function field(string $name, string $title, string $id = '', $value = null, bool $has_bug = false, $path = null)
    {
        $input = parent::field($name, $title, $id, $value, $has_bug, $path);

        return  $input.
            "<span class='input-group-append'>
                <button class='btn btn-primary' data-icon='{$input->getAttribute('value')}' data-load='picker::icon'></button>
            </span>";
    }
}