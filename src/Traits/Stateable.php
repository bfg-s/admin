<?php

namespace Admin\Traits;

trait Stateable
{
    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->state($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->state[$name] = $value;
    }

    /**
     * State getter.
     *
     * @param  string  $name
     * @param  null  $default
     * @return mixed
     */
    public function state(string $name, $default = null)
    {
        return isset($this->state) && isset($this->state[$name]) ? $this->state[$name] : $default;
    }

    /**
     * State getter.
     *
     * @param  string  $name
     * @param  null  $default
     * @return mixed
     */
    public function requestState(string $name, $default = null)
    {
        return request()->get($name, $this->state($name, $default));
    }

    /**
     * @return array|mixed
     */
    public function states()
    {
        return $this->state ?? [];
    }

    /**
     * @param  array  $default
     * @return $this
     */
    public function createState(array $default = [])
    {
        $this->state = $default;

        return $this;
    }
}
