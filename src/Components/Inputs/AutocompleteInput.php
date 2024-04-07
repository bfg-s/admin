<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

class AutocompleteInput extends SelectInput
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-tag';

    /**
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
     * @param $vals
     * @return void
     */
    protected function setSubjectValues($vals): void
    {
        if (is_array($vals) && count($vals)) {
            $this->options($vals, true);
        } else {
            $this->options([$this->value]);
        }
    }
}
