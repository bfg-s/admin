<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input admin panel autocomplete.
 */
class AutocompleteInput extends SelectInput
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-tag';

    /**
     * AutocompleteInput constructor.
     *
     * @param  string  $name
     * @param  string|null  $title
     * @param ...$params
     */
    public function __construct(string $name, string $title = null, ...$params)
    {
        parent::__construct($name, $title, $params);

        $this->data['tags'] = 'true';
    }

    /**
     * Set values for selection.
     *
     * @param $values
     * @return void
     */
    protected function setSubjectValues($values): void
    {
        if (is_array($values) && count($values)) {
            $this->options($values, true);
        } else {
            $this->options([$this->value]);
        }
    }
}
