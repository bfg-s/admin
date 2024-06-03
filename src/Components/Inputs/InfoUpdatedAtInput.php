<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input the admin panel to display the "updated at" field.
 */
class InfoUpdatedAtInput extends InfoInput
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-quote-right';

    /**
     * InfoUpdatedAtInput constructor.
     *
     * @param  string  $name
     * @param  string  $title
     * @param ...$params
     */
    public function __construct(string $name = 'updated_at', string $title = 'admin.updated_at', ...$params)
    {
        parent::__construct($name, $title, $params);
    }
}
