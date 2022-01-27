<?php

namespace Lar\LteAdmin\Components\Fields;

use Illuminate\Contracts\Support\Arrayable;

class MultiSelectField extends SelectField
{
    /**
     * @var array[]
     */
    protected $params = [
        ['multiple' => 'multiple'],
    ];

    /**
     * @var null
     */
    protected $icon = null;

    /**
     * @param  array|Arrayable  $options
     * @param  bool  $first_default
     * @return MultiSelectField
     */
    public function options($options, bool $first_default = false)
    {
        return parent::options($options, $this->load_subject ? false : $first_default);
    }
}
