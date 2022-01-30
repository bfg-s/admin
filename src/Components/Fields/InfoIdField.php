<?php

namespace LteAdmin\Components\Fields;

class InfoIdField extends InfoField
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-quote-right';

    public function __construct(string $name = 'id', string $title = 'lte.id', ...$params)
    {
        parent::__construct($name, $title, $params);
    }
}
