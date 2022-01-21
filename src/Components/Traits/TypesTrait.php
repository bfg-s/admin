<?php

namespace Lar\LteAdmin\Components\Traits;

trait TypesTrait
{
    /**
     * @var string
     */
    protected $type = 'info';

    /**
     * @return $this
     */
    public function primaryType()
    {
        $this->type = 'primary';

        return $this;
    }

    /**
     * @return $this
     */
    public function secondaryType()
    {
        $this->type = 'secondary';

        return $this;
    }

    /**
     * @return $this
     */
    public function successType()
    {
        $this->type = 'success';

        return $this;
    }

    /**
     * @return $this
     */
    public function dangerType()
    {
        $this->type = 'danger';

        return $this;
    }

    /**
     * @return $this
     */
    public function warningType()
    {
        $this->type = 'warning';

        return $this;
    }

    /**
     * @return $this
     */
    public function infoType()
    {
        $this->type = 'info';

        return $this;
    }

    /**
     * @return $this
     */
    public function lightType()
    {
        $this->type = 'light';

        return $this;
    }

    /**
     * @return $this
     */
    public function darkType()
    {
        $this->type = 'dark';

        return $this;
    }
}
