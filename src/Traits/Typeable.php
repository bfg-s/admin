<?php

declare(strict_types=1);

namespace Admin\Traits;

/**
 * Element display type trade.
 */
trait Typeable
{
    /**
     * Default type.
     *
     * @var string|null
     */
    protected string|null $type = 'info';

    /**
     * Set the type of element to primary.
     *
     * @return static
     */
    public function primaryType(): static
    {
        $this->type = 'primary';

        return $this;
    }

    /**
     * Set the type of element to secondary.
     *
     * @return static
     */
    public function secondaryType(): static
    {
        $this->type = 'secondary';

        return $this;
    }

    /**
     * Set the type of element to success.
     *
     * @return static
     */
    public function successType(): static
    {
        $this->type = 'success';

        return $this;
    }

    /**
     * Set the type of element to danger.
     *
     * @return static
     */
    public function dangerType(): static
    {
        $this->type = 'danger';

        return $this;
    }

    /**
     * Set the type of element to warning.
     *
     * @return static
     */
    public function warningType(): static
    {
        $this->type = 'warning';

        return $this;
    }

    /**
     * Set the type of element to info.
     *
     * @return static
     */
    public function infoType(): static
    {
        $this->type = 'info';

        return $this;
    }

    /**
     * Set the type of element to light.
     *
     * @return static
     */
    public function lightType(): static
    {
        $this->type = 'light';

        return $this;
    }

    /**
     * Set the type of element to dark.
     *
     * @return static
     */
    public function darkType(): static
    {
        $this->type = 'dark';

        return $this;
    }

    /**
     * Set the type of element to a custom value.
     *
     * @param  string  $type
     * @return static
     */
    public function visibleType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
