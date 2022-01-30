<?php

namespace LteAdmin\Components;

use Closure;

class ModelRelationContentComponent extends Component
{
    protected $class = 'template_container';

    /**
     * @var Closure|array|null
     */
    protected $control_group = null;

    /**
     * @var Closure|array|null
     */
    protected $control_delete = false;

    /**
     * @var Closure|array|null
     */
    protected $control_create = false;

    /**
     * @var Closure|array|null
     */
    protected $control_restore = null;

    /**
     * @var string|null
     */
    protected $control_restore_text = '';

    /**
     * @var callable[]
     */
    protected $controls = [];

    /**
     * @var bool
     */
    protected $vertical = true;

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

    public function fullControl()
    {
        $this->controlCreate(true);
        $this->controlRestore(true);
        $this->controlDelete(true);

        return $this;
    }

    /**
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlCreate($test = null)
    {
        $this->set_test_var('control_create', $test);

        return $this;
    }

    /**
     * @param  string  $var_name
     * @param $test
     */
    protected function set_test_var(string $var_name, $test)
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
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlRestore($test = null)
    {
        $this->set_test_var('control_restore', $test);

        return $this;
    }

    /**
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlDelete($test = null)
    {
        $this->set_test_var('control_delete', $test);

        return $this;
    }

    public function controls(callable $call)
    {
        $this->controls[] = $call;

        return $this;
    }

    /**
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlGroup($test = null)
    {
        $this->set_test_var('control_group', $test);

        return $this;
    }

    /**
     * @param  string  $text
     * @return $this
     */
    public function controlRestoreText(string $text)
    {
        $this->control_restore_text = $text;

        return $this;
    }

    /**
     * @param  string  $var_name
     * @param  array  $args
     * @return bool
     */
    public function get_test_var(string $var_name, array $args = [])
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
    public function callControls(...$params)
    {
        foreach ($this->controls as $control) {
            call_user_func_array($control, $params);
        }
    }

    /**
     * @return bool
     */
    public function hasControls()
    {
        return (bool) count($this->controls);
    }

    protected function mount()
    {
        //
    }
}
