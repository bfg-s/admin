<?php

namespace Lar\LteAdmin\Components\Fields;

class AutocompleteField extends SelectField
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-tag';

    public function __construct(string $name, string $title = null, ...$params)
    {
        parent::__construct($name, $title, $params);

        $this->data['tags'] = 'true';
    }
}
