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

    /**
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $options
     * @param  bool  $first_default
     * @return MultiSelect
     */
    public function options($options, bool $first_default = false)
    {
        return parent::options($options, $this->load_subject ? false: $first_default);
    }
}