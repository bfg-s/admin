<?php

namespace Lar\LteAdmin\Components\Fields;

use Illuminate\Contracts\Support\Arrayable;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\INPUT;
use Lar\LteAdmin\Components\Cores\CheckBoxFieldCore;
use Lar\LteAdmin\Components\FormGroupComponent;

class ChecksField extends FormGroupComponent
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var null
     */
    protected $icon = null;

    /**
     * Checks constructor.
     * @param  string  $name
     * @param  string|null  $title
     * @param ...$params
     */
    public function __construct(string $name, string $title = null, ...$params)
    {
        parent::__construct($name, $title, $params);

        if (!request()->has($this->path) && !request()->has('__only_has')) {
            request()->request->add(
                array_dots_uncollapse(
                    [$this->path => []],
                    request()->all()
                )
            );
        }
    }

    /**
     * @return Component|INPUT|mixed
     */
    public function field()
    {
        return CheckBoxFieldCore::create($this->options)
            ->name($this->name)
            ->id($this->field_id)
            ->value($this->value)
            ->setRules($this->rules)
            ->setDatas($this->data);
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
     * @return $this|ChecksField|FormGroupComponent
     */
    public function _front_rule_required()
    {
        $this->rules[] = 'any-checked';

        return $this;
    }

    /**
     * Make wrapper for input.
     */
    protected function makeWrapper()
    {
        parent::makeWrapper();
    }
}
