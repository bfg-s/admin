<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Admin\Components\Inputs\Parts\InputCheckBoxComponent;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Input admin panel checkbox.
 */
class ChecksInput extends InputGroupComponent
{
    /**
     * Checkbox options.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * ChecksInput constructor.
     *
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
     * Method for creating an input field.
     *
     * @return mixed
     */
    public function field(): mixed
    {
        return InputCheckBoxComponent::create($this->options)
            ->name($this->name)
            ->id($this->field_id)
            ->value($this->value)
            ->setDatas($this->data)
            ->setRules($this->rules)
            ->setAttributes($this->attributes);
    }

    /**
     * Add checkbox options.
     *
     * @param  array|Arrayable  $options
     * @param  bool  $first_default
     * @return $this
     */
    public function options(array|Arrayable $options, bool $first_default = false): static
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
}
