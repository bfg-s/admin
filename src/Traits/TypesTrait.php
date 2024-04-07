<?php

declare(strict_types=1);

namespace Admin\Traits;

trait TypesTrait
{
    /**
     * @var string|null
     */
    protected ?string $type = 'info';

    /**
     * @return static
     */
    public function primaryType(): static
    {
        $this->type = 'primary';

        return $this;
    }

    /**
     * @return static
     */
    public function secondaryType(): static
    {
        $this->type = 'secondary';

        return $this;
    }

    /**
     * @return static
     */
    public function successType(): static
    {
        $this->type = 'success';

        return $this;
    }

    /**
     * @return static
     */
    public function dangerType(): static
    {
        $this->type = 'danger';

        return $this;
    }

    /**
     * @return static
     */
    public function warningType(): static
    {
        $this->type = 'warning';

        return $this;
    }

    /**
     * @return static
     */
    public function infoType(): static
    {
        $this->type = 'info';

        return $this;
    }

    /**
     * @return static
     */
    public function lightType(): static
    {
        $this->type = 'light';

        return $this;
    }

    /**
     * @return static
     */
    public function darkType(): static
    {
        $this->type = 'dark';

        return $this;
    }

    /**
     * @param  string  $type
     * @return static
     */
    public function visibleType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
