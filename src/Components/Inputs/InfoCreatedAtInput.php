<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input the admin panel to display the "created at" field.
 */
class InfoCreatedAtInput extends InfoInput
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-quote-right';

    /**
     * InfoCreatedAtInput constructor.
     *
     * @param  string  $name
     * @param  string  $title
     * @param ...$params
     */
    public function __construct(string $name = 'created_at', string $title = 'admin.created_at', ...$params)
    {
        parent::__construct($name, $title, $params);
    }
}
