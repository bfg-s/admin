<?php

namespace LteAdmin\Components\Fields;

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
    protected function loadSubject()
    {
        $selector = new Select2(
            $this->load_subject,
            $this->load_format,
            $this->value,
            $this->nullable ? $this->title : null,
            $this->field_id.'_',
            $this->load_where,
            $this->separator
        );

        $r_name = $selector->getName();

        if (request()->has($r_name)) {
            exit($selector->toJson(JSON_UNESCAPED_UNICODE));
        }

        $this->data['select-name'] = $r_name;

        $this->on_load('select2::ajax');

        $vals = $selector->getValueData();

        if (count($vals)) {
            $this->options($vals, true);
        } else {
            $this->options([$this->value]);
        }
    }
}
