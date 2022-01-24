<?php

namespace Lar\LteAdmin\Components\Traits;

trait TypesTrait
{
    /**
     * @var string
     */
    protected $type = 'info';

    /**
     * @return static
     */
    public function primaryType()
    {
        $this->type = 'primary';

        return $this;
    }

    /**
     * @return static
     */
    public function secondaryType()
    {
        $this->type = 'secondary';

        return $this;
    }

    /**
     * @return static
     */
    public function successType()
    {
        $this->type = 'success';

        return $this;
    }

    /**
     * @return static
     */
    public function dangerType()
    {
        $this->type = 'danger';

        return $this;
    }

    /**
     * @return static
     */
    public function warningType()
    {
        $this->type = 'warning';

        return $this;
    }

    /**
     * @return static
     */
    public function infoType()
    {
        $this->type = 'info';

        return $this;
    }

    /**
     * @return static
     */
    public function lightType()
    {
        $this->type = 'light';

        return $this;
    }

    /**
     * @return static
     */
    public function darkType()
    {
        $this->type = 'dark';

        return $this;
    }
}
