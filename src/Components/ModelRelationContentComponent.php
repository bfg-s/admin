<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Content relationship component of the admin panel model.
 */
class ModelRelationContentComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'model-relation-content';

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
     * Text for the model relationship restore button.
     *
     * @var string|null
     */
    protected ?string $control_restore_text = '';

    /**
     * Vertical display of input and label.
     *
     * @var bool
     */
    protected bool $vertical = true;

    /**
     * ModelRelationContentComponent constructor.
     *
     * @param  string  $relation
     * @param  string  $name
     * @param ...$delegates
     */
    public function __construct(string $relation, string $name, ...$delegates)
    {
        parent::__construct(...$delegates);

        $this->setDatas([
            "relation-{$relation}" => $name,
        ]);
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
     * Set the text for the restore model relationship button.
     *
     * @param  string  $text
     * @return $this
     */
    public function controlRestoreText(string $text): static
    {
        $this->control_restore_text = $text;

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
            return call_user_func_array($this->{$var_name}, $args);
        }

        return true;
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
