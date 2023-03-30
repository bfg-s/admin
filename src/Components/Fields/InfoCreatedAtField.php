<?php

namespace Admin\Components\Fields;

class InfoCreatedAtField extends InfoField
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-quote-right';

    public function __construct(string $name = 'created_at', string $title = 'admin.created_at', ...$params)
    {
        parent::__construct($name, $title, $params);
    }
}
