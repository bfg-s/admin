<?php

declare(strict_types=1);

namespace Admin\Traits;

use Admin\Components\FieldComponent;
use Admin\Components\FormGroupComponent;

trait FieldMassControlTrait
{
    /**
     * @var bool
     */
    protected bool $vertical = false;

    /**
     * @var bool
     */
    protected bool $reversed = false;

    /**
     * @var bool
     */
    protected bool $set = true;

    /**
     * @var int|null
     */
    protected ?int $label_width = 2;

    /**
     * @param $name
     * @param  array  $arguments
     * @return bool|FormGroupComponent|mixed
     */
    public static function static_call_group($name, array $arguments): mixed
    {
        return (new FieldComponent())->{$name}(...$arguments);
    }

    /**
     * @return $this
     */
    public function vertical(): static
    {
        $this->vertical = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function horizontal(): static
    {
        $this->vertical = false;

        return $this;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function if($condition): static
    {
        $this->set = $condition;

        return $this;
    }

    /**
     * @return $this
     */
    public function reversed(): static
    {
        $this->reversed = true;

        return $this;
    }

    /**
     * @param  int  $width
     * @return $this
     */
    public function label_width(int $width): static
    {
        $this->label_width = $width;

        return $this;
    }

    /**
     * @param $name
     * @param  array  $arguments
     * @return bool|FormGroupComponent|mixed
     */
    protected function call_group($name, array $arguments): mixed
    {
        if (isset(static::$inputs[$name])) {
            $class = static::$inputs[$name];

            $class = new $class(...$arguments);

            if ($class instanceof FormGroupComponent) {
                $class->set_parent($this);

                $class->model($this->model);

                if ($this->vertical) {
                    $class->vertical();
                }

                if ($this->reversed) {
                    $class->reversed();
                }

                if ($this->label_width !== null) {
                    $class->label_width($this->label_width);
                }
            }

            if ($this->set) {
                $this->appEnd($class);
            } else {
                $class->unregister();
            }

            $this->set = true;

            return $class;
        }

        return false;
    }
}
