<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Admin\Components\Inputs\Parts\InputSelect2Component;
use Admin\Core\Select2;
use App;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Input admin panel for selecting data from a drop-down list.
 */
class SelectInput extends InputGroupComponent
{
    /**
     * List of callbacks for loading options for select.
     *
     * @var array
     */
    public static array $loadCallBacks = [];

    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-mouse-pointer';

    /**
     * List of options for select.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'select2::init',
        'theme' => 'bootstrap4',
    ];

    /**
     * The subject of loading options for the select.
     *
     * @var mixed
     */
    protected mixed $load_subject = null;

    /**
     * Format for displaying options for select.
     *
     * @var string|null
     */
    protected ?string $load_format = null;

    /**
     * Closure for additional description of the selection from the subject of the options for the select.
     *
     * @var mixed|callable
     */
    protected mixed $load_where;

    /**
     * Input may be null.
     *
     * @var bool
     */
    protected bool $nullable = false;

    /**
     * Input the admin panel with multi-selection.
     *
     * @var bool
     */
    protected bool $multiple = false;

    /**
     * The field by which selection options are sorted.
     *
     * @var string|null
     */
    protected string|null $orderBy = null;

    /**
     * Option sorting type for select.
     *
     * @var string
     */
    protected string $orderType = 'ASC';

    /**
     * Method for creating an input field.
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function field(): mixed
    {
        if ($this->load_subject) {
            $this->loadSubject();
        }

        $this->data['placeholder'] = $this->placeholder ?: $this->title;

        return InputSelect2Component::create($this->options)
            ->setAttributes($this->attributes)
            ->setName($this->name)
            ->setId($this->field_id)
            ->setValues($this->value)
            ->setMultiple($this->multiple)
            ->setHasBug($this->has_bug)
            ->makeOptions()
            ->setDatas($this->data);
    }

    /**
     * A method to create a subject data load for a selector.
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function loadSubject(): void
    {
        $selector = new Select2(
            $this->load_subject,
            $this->load_format,
            $this->value,
            $this->nullable ? $this->title : null,
            $this->field_id.'_',
            $this->load_where,
            $this->orderBy,
            $this->orderType
        );

        $r_name = $selector->getName();

        static::$loadCallBacks[$r_name] = $selector;

        $this->data['select-name'] = $r_name;

        $this->data['load'] = 'select2::ajax';

        $vals = $selector->getValueData();

        $this->setSubjectValues($vals);
    }

    /**
     * Set values for selection.
     *
     * @param $values
     * @return void
     */
    protected function setSubjectValues($values): void
    {
        if ($values) {
            $this->options($values, true);
        }
    }

    /**
     * Add options to the current input.
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

        $lang = App::getLocale();

        foreach ($options as $k => $option) {
            $this->options[$k] = $option;
        }

        foreach ($this->options as $k => $option) {
            if ($option && is_array($option)) {
                $this->options[$k] = $option[$lang] ?? implode(', ', $option);
            } else {
                $this->options[$k] = $option;
            }
        }

        if ($first_default && !$this->nullable) {
            $this->default(array_key_first($this->options));
        }

        return $this;
    }

    /**
     * Set the sort field and sort type to "Desc".
     *
     * @param  string  $field
     * @return $this
     */
    public function orderByDesc(string $field): static
    {
        return $this->orderBy($field, 'DESC');
    }

    /**
     * Set the sort field and any sort type.
     *
     * @param  string  $field
     * @param  string  $type
     * @return $this
     */
    public function orderBy(string $field, string $type = 'ASC'): static
    {
        $this->orderBy = $field;
        $this->orderType = $type;

        return $this;
    }

    /**
     * Describe the data loading properties for the select.
     *
     * @param $subject
     * @param  string  $format
     * @param  callable|null  $where
     * @return $this
     */
    public function load($subject, string $format = '{id}) {name}', callable $where = null): static
    {
        $this->load_subject = $subject;
        $this->load_format = $format;
        $this->load_where = $where;

        if ($where) {
            $this->data['with-where'] = 'true';
        }

        return $this;
    }

    /**
     * Make the current input null.
     *
     * @param  string|null  $message
     * @return static
     */
    public function nullable(string $message = null): static
    {
        $this->nullable = true;

        if ($this->options) {
            $opts = ['' => ''];
            foreach ($this->options as $k => $option) {
                $opts[$k] = $option;
            }
            $this->options = $opts;
        } else {
            $this->options = ['' => ''];
        }

        $this->data['allow-clear'] = 'true';

        return parent::nullable($message);
    }
}
