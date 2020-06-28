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
     * @var array
     */
    protected $params = [
        ['autocomplete' => 'new-password']
    ];

    /**
     * @param  string|null  $label
     * @return $this
     */
    public function confirm(string $label = null)
    {
        $this->_front_rule_equal_to("#input_{$this->name}_confirmation")->confirmed()->crypt();

        if (!$label && $this->title) {

            $label = $this->title . " " . __('lte.confirmation');
        }

        $this->parent_field->password($this->name . '_confirmation', $label, ...$this->params)
            ->icon($this->icon)->mergeDataList($this->data)->_front_rule_equal_to("#input_{$this->name}");

        return $this;
    }

    /**
     * @return mixed
     */
    protected function create_value()
    {
        return $this->value;
    }
}