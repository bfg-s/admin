<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input the admin panel to display the "id" field.
 */
class InfoIdInput extends InfoInput
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-quote-right';

    /**
     * InfoIdInput constructor.
     *
     * @param  string  $name
     * @param  string  $title
     * @param ...$params
     */
    public function __construct(string $name = 'id', string $title = 'admin.id', ...$params)
    {
        parent::__construct($name, $title, $params);
    }
}
