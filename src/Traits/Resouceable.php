<?php

namespace Admin\Traits;

trait Resouceable
{
    /**
     * Special resource for the model.
     *
     * @var string|null
     */
    protected string|null $resource = null;

    /**
     * Set special resource for the model.
     *
     * @param  string  $class
     * @return $this
     */
    public function resource(string $class): static
    {
        $this->resource = $class;

        return $this;
    }

    /**
     * Get special resource for the model.
     *
     * @return string|null
     */
    public function getResource(): ?string
    {
        if (! $this->resource) {
            $controller = admin_repo()->currentController;
            if ($controller) {
                if (method_exists($controller, 'resource')) {
                    $this->resource = $controller->resource();
                } else if (property_exists($controller, 'resource')) {
                    $this->resource = $controller::$resource;
                }
            }
        }

        return $this->resource;
    }
}
