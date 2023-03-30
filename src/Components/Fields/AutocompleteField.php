<?php

namespace Admin\Components\Fields;

class AutocompleteField extends SelectField
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-tag';

    public function __construct(string $name, string $title = null, ...$params)
    {
        parent::__construct($name, $title, $params);

        $this->data['tags'] = 'true';
    }

    /**
     * @return void
     */
    protected function setSubjectValues($vals)
    {
        if (is_array($vals) && count($vals)) {
            $this->options($vals, true);
        } else {
            $this->options([$this->value]);
        }
    }
}
