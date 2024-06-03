<?php

declare(strict_types=1);

namespace Admin\Components\Inputs\Parts;

use Admin\Components\Component;

/**
 * Input admin panel for select2 component tags.
 */
class InputSelect2TagsComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'inputs.parts.input-select2';

    /**
     * Prepared options for select2 components.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Options for select2 components.
     *
     * @var array
     */
    protected array $optionsPrint = [];

    /**
     * The current value of select2 components.
     *
     * @var mixed|null
     */
    protected mixed $value = null;

    /**
     * The current name of select2 components.
     *
     * @var string|null
     */
    protected string|null $name = null;

    /**
     * Current select2 component ID.
     *
     * @var string|null
     */
    protected string|null $id = null;

    /**
     * Does select2 component have an error.
     *
     * @var bool
     */
    protected bool $hasBug;

    /**
     * Date tags for select2 component.
     *
     * @var array|string[]
     */
    protected array $datas = [
        'tags' => 'true'
    ];

    /**
     * InputSelect2TagsComponent constructor.
     *
     * @param $options
     * @param ...$delegates
     */
    public function __construct($options, ...$delegates)
    {
        parent::__construct($delegates);
        $this->options = $options;
    }

    /**
     * Add date tags for select2 component.
     *
     * @param  array  $datas
     * @return $this
     */
    public function setDatas(array $datas): static
    {
        $this->datas = array_merge($this->datas, $datas);

        return $this;
    }

    /**
     * Create options from prepared options.
     *
     * @return $this
     */
    public function makeOptions(): static
    {
        if (is_array($this->value)) {
            foreach ($this->value as $item) {
                $key = array_search($item, $this->options);
                if ($key !== false) {
                    unset($this->options[$key]);
                }
                $this->optionsPrint[(string) $item] = [$item];
            }
        }

        foreach ($this->options as $option) {
            $this->optionsPrint[(string) $option] = $option;
        }

        return $this;
    }

    /**
     * Set the value for select 2 components.
     *
     * @param $value
     * @return $this
     */
    public function setValues($value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the name select 2 components.
     *
     * @param  mixed  $name
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set ID select 2 components.
     *
     * @param  mixed  $id
     */
    public function setId($id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Determine whether the select error has 2 components.
     *
     * @param  bool  $hasBug
     * @return $this
     */
    public function setHasBug(bool $hasBug): static
    {
        $this->hasBug = $hasBug;

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'options' => $this->optionsPrint,
            'name' => $this->name,
            'id' => $this->id,
            'hasBug' => $this->hasBug,
            'datas' => $this->datas,
            'multiple' => true,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
