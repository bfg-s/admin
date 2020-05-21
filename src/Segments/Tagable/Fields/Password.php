<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;


/**
 * Class Password
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Password extends Input
{
    /**
     * @var string
     */
    protected $type = "password";

    /**
     * @var string
     */
    protected $icon = "fas fa-key";

    /**
     * @param  string|null  $label
     * @return $this
     */
    public function confirmed(string $label = null)
    {
        //$this->rules[] = "confirmation";

        $this->isEqualTo("#input_{$this->name}_confirmation");

        if (!$label && $this->title) {

            $label = $this->title . " confirmation";
        }

        $this->parent_field->password($this->name . '_confirmation', $label, ...$this->params)
            ->icon($this->icon)->mergeDataList($this->data)->isEqualTo("#input_{$this->name}");

        return $this;
    }
}