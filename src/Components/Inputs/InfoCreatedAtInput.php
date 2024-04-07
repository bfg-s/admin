<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

class InfoCreatedAtInput extends InfoInput
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
    public function __construct(string $name = 'created_at', string $title = 'admin.created_at', ...$params)
    {
        parent::__construct($name, $title, $params);
    }
}
