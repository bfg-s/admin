<?php

declare(strict_types=1);

namespace Admin\Traits;

/**
 * Trait assistant that adds the ability to control the shape and types of inputs.
 */
trait ComponentInputControlTrait
{
    /**
     * Vertical display mode modifier.
     *
     * @var bool
     */
    protected bool $vertical = false;

    /**
     * Reverse display mode modifier.
     *
     * @var bool
     */
    protected bool $reversed = false;

    /**
     * Condition for displaying the input.
     *
     * @var bool
     */
    protected bool $set = true;

    /**
     * Width of the input label in columns.
     *
     * @var int|null
     */
    protected ?int $labelWidth = 2;

    /**
     * Enable horizontal mode.
     *
     * @return $this
     */
    public function horizontal(): static
    {
        $this->vertical = false;

        return $this;
    }

    /**
     * Add a condition for displaying the input.
     *
     * @param $condition
     * @return $this
     */
    public function if($condition): static
    {
        $this->set = $condition;

        return $this;
    }

    /**
     * Enable vertical input mode.
     *
     * @return $this
     */
    public function vertical(): static
    {
        $this->vertical = true;

        return $this;
    }

    /**
     * Enable reverse input mode.
     *
     * @return $this
     */
    public function reversed(): static
    {
        $this->reversed = true;

        return $this;
    }

    /**
     * Set the width of the input label in columns.
     *
     * @param  int  $width
     * @return $this
     */
    public function label_width(int $width): static
    {
        $this->labelWidth = $width;

        return $this;
    }
}
