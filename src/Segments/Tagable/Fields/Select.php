<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Illuminate\Contracts\Support\Arrayable;
use Lar\Developer\Core\Select2;
use Lar\LteAdmin\Segments\Tagable\Cores\CoreSelect2;
use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Select
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Select extends FormGroup
{
    /**
     * @var string
     */
    protected $icon = "fas fa-mouse-pointer";

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $class = "form-control";

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'select2'
    ];

    /**
     * @var mixed
     */
    protected $load_subject;

    /**
     * @var string
     */
    protected $load_format;

    /**
     * @var bool
     */
    protected $nullable = false;

    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        if ($this->load_subject) {
            $selector = new Select2(
                $this->load_subject,
                $this->load_format,
                $this->value,
                $this->nullable ? $this->title : null,
                $this->name . '_'
            );

            $r_name = $selector->getName();

            if (request()->has($r_name)) { exit($selector->toJson(JSON_UNESCAPED_UNICODE)); }

            $this->on_load('select2::ajax', $r_name)
                ->options($selector->getValueData(), true);
        }

        return CoreSelect2::create($this->options, [
            'name' => $this->name,
            'data-placeholder' => $this->title
        ], ...$this->params)
            ->setValues($this->value)
            ->makeOptions()
            ->setDatas($this->data)
            ->addClassIf($this->has_bug, 'is-invalid')
            ->addClass($this->class);
    }

    /**
     * @param  array|Arrayable  $options
     * @param  bool  $first_default
     * @return $this
     */
    public function options($options, bool $first_default = false)
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = $options;

        if ($first_default) {
            $this->default(array_key_first($this->options));
        }

        return $this;
    }

    /**
     * @param $subject
     * @param  string  $format
     * @return $this
     */
    public function load($subject, string $format = null)
    {
        $this->load_subject = $subject;
        $this->load_format = $format;

        return $this;
    }

    /**
     * @param  string|null  $message
     * @return Select
     */
    public function nullable(string $message = null)
    {
        $this->nullable = true;

        return parent::nullable($message);
    }
}