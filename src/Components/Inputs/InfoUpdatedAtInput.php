<?php

namespace Admin\Components\Inputs;

class InfoUpdatedAtInput extends InfoInput
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-quote-right';

    /**
     * @param  string  $name
     * @param  string  $title
     * @param ...$params
     */
    public function __construct(string $name = 'updated_at', string $title = 'admin.updated_at', ...$params)
    {
        parent::__construct($name, $title, $params);
    }
}
