<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Relation container component of the admin panel model.
 */
class ModelRelationContainerComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'model-relation-container';

    /**
     * Control marker by a group of control buttons.
     *
     * @var mixed
     */
    protected mixed $control_group = null;

    /**
     * Control marker with delete button.
     *
     * @var mixed
     */
    protected mixed $control_delete = false;

    /**
     * Control marker with the create button.
     *
     * @var mixed
     */
    protected mixed $control_create = false;

    /**
     * Control marker with recovery button.
     *
     * @var mixed
     */
    protected mixed $control_restore = null;

    /**
     * Vertical display of input and label.
     *
     * @var bool
     */
    protected bool $vertical = true;

    /**
     * Groups of custom model relation container buttons.
     *
     * @var array
     */
    protected array $buttons = [];

    /**
     * A property that describes the column by which the model relation is sorted. If not specified, sorting is disabled.
     *
     * @var string|null
     */
    protected ?string $ordered = null;

    /**
     * ModelRelationContainerComponent constructor.
     *
     * @param  string  $relation
     * @param  string|int  $name
     * @param ...$delegates
     */
    public function __construct(string $relation, string|int $name, ...$delegates)
    {
        parent::__construct(...$delegates);

        $this->setDatas([
            "relation-{$relation}" => $name,
        ]);
    }

    /**
     * Set the model relation field to be sorted.
     *
     * @param  string|null  $ordered
     * @return $this
     */
    public function setOrdered(?string $ordered): static
    {
        $this->ordered = $ordered;

        return $this;
    }

    /**
     * Add a group of buttons to the model relation.
     *
     * @param  mixed  $buttons
     * @return static
     */
    public function setButtons(mixed $buttons): static
    {
        $this->buttons[] = $buttons;

        return $this;
    }

    /**
     * Enable all model relationship control buttons.
     *
     * @return $this
     */
    public function fullControl(): static
    {
        $this->controlCreate(true);
        $this->controlRestore(true);
        $this->controlDelete(true);

        return $this;
    }

    /**
     * Enable or disable the model relationship creation button.
     *
     * @param  mixed|null  $test
     * @return $this
     */
    public function controlCreate(mixed $test = null): static
    {
        $this->set_test_var('control_create', $test);

        return $this;
    }

    /**
     * Enable or disable the restore model relationship button.
     *
     * @param  mixed|null  $test
     * @return $this
     */
    public function controlRestore(mixed $test = null): static
    {
        $this->set_test_var('control_restore', $test);

        return $this;
    }

    /**
     * Enable or disable the delete button by model relationships.
     *
     * @param  mixed|null  $test
     * @return $this
     */
    public function controlDelete(mixed $test = null): static
    {
        $this->set_test_var('control_delete', $test);

        return $this;
    }

    /**
     * Enable or disable a group of model relationship management buttons.
     *
     * @param  mixed|null  $test
     * @return $this
     */
    public function controlGroup(mixed $test = null): static
    {
        $this->set_test_var('control_group', $test);

        return $this;
    }

    /**
     * Set the control check value using model relationship buttons.
     *
     * @param  string  $var_name
     * @param $test
     */
    protected function set_test_var(string $var_name, $test): void
    {
        if (is_embedded_call($test)) {
            $this->{$var_name} = $test;
        } else {
            $this->{$var_name} = static function () use ($test) {
                return (bool) $test;
            };
        }
    }

    /**
     * Get the control check value of the model relationship buttons.
     *
     * @param  string  $var_name
     * @param  array  $args
     * @return mixed
     */
    public function get_test_var(string $var_name, array $args = []): mixed
    {
        if (is_bool($this->{$var_name}) || is_string($this->{$var_name})) {
            return $this->{$var_name};
        } elseif ($this->{$var_name} !== null) {
            $call = $this->{$var_name};
            if (is_callable($call)) {
                return call_user_func_array($call, $args);
            }
        }

        return true;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array|null[]
     */
    protected function viewData(): array
    {
        return [
            'buttons' => $this->buttons,
            'ordered' => $this->ordered,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        //
    }
}
