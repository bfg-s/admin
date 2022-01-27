<?php

namespace Lar\LteAdmin\Components\Traits;

use Lar\LteAdmin\Components\FieldComponent;
use Lar\LteAdmin\Components\FormGroupComponent;

trait FieldMassControlTrait
{
    /**
     * @var bool
     */
    protected $vertical = false;

    /**
     * @var bool
     */
    protected $reversed = false;

    /**
     * @var bool
     */
    protected $set = true;

    /**
     * @var int|null
     */
    protected $label_width;

    /**
     * @param $name
     * @param  array  $arguments
     * @return bool|FormGroupComponent|mixed
     */
    public static function static_call_group($name, array $arguments)
    {
        return (new FieldComponent())->{$name}(...$arguments);
    }

    /**
     * @return $this
     */
    public function vertical()
    {
        $this->vertical = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function horizontal()
    {
        $this->vertical = false;

        return $this;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function if($condition)
    {
        $this->set = $condition;

        return $this;
    }

    /**
     * @return $this
     */
    public function reversed()
    {
        $this->reversed = true;

        return $this;
    }

    /**
     * @param  int  $width
     * @return $this
     */
    public function label_width(int $width)
    {
        $this->label_width = $width;

        return $this;
    }

    /**
     * @param $name
     * @param  array  $arguments
     * @return bool|FormGroupComponent|mixed
     */
    protected function call_group($name, array $arguments)
    {
        if (isset(static::$inputs[$name])) {
            $class = static::$inputs[$name];

            $class = new $class(...$arguments);

            if ($class instanceof FormGroupComponent) {
                $class->set_parent($this);

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
