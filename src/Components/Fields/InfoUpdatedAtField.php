<?php

namespace Lar\LteAdmin\Components\Fields;

class InfoUpdatedAtField extends InfoField
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-quote-right';

    public function __construct(string $name = 'updated_at', string $title = 'lte.updated_at', ...$params)
    {
        parent::__construct($name, $title, $params);
    }
}
