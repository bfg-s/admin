<?php

namespace Admin\Components;

use Closure;

class ModelRelationContentComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'model-relation-content';

    /**
     * @var mixed
     */
    protected mixed $control_group = null;

    /**
     * @var mixed
     */
    protected mixed $control_delete = false;

    /**
     * @var mixed
     */
    protected mixed $control_create = false;

    /**
     * @var mixed
     */
    protected mixed $control_restore = null;

    /**
     * @var string|null
     */
    protected ?string $control_restore_text = '';

    /**
     * @var callable[]
     */
    protected array $controls = [];

    /**
     * @var bool
     */
    protected bool $vertical = true;

    /**
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
     * @param  mixed|null  $test
     * @return $this
     */
    public function controlCreate(mixed $test = null): static
    {
        $this->set_test_var('control_create', $test);

        return $this;
    }

    /**
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
     * @param  mixed|null  $test
     * @return $this
     */
    public function controlRestore(mixed $test = null): static
    {
        $this->set_test_var('control_restore', $test);

        return $this;
    }

    /**
     * @param  mixed|null  $test
     * @return $this
     */
    public function controlDelete(mixed $test = null): static
    {
        $this->set_test_var('control_delete', $test);

        return $this;
    }

    /**
     * @param  callable  $call
     * @return $this
     */
    public function controls(callable $call): static
    {
        $this->controls[] = $call;

        return $this;
    }

    /**
     * @param  mixed|null  $test
     * @return $this
     */
    public function controlGroup(mixed $test = null): static
    {
        $this->set_test_var('control_group', $test);

        return $this;
    }

    /**
     * @param  string  $text
     * @return $this
     */
    public function controlRestoreText(string $text): static
    {
        $this->control_restore_text = $text;

        return $this;
    }

    /**
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
     * @param  mixed  ...$params
     */
    public function callControls(...$params): void
    {
        foreach ($this->controls as $control) {
            call_user_func_array($control, $params);
        }
    }

    /**
     * @return bool
     */
    public function hasControls(): bool
    {
        return (bool) count($this->controls);
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        //
    }
}
