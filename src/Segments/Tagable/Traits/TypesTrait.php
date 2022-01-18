<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits;

/**
 * Trait TypesTrait.
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait TypesTrait
{
    /**
     * @var string
     */
    protected $type = 'info';

    /**
     * @return $this
     */
    public function primary()
    {
        $this->type = 'primary';

        return $this;
    }

    /**
     * @return $this
     */
    public function secondary()
    {
        $this->type = 'secondary';

        return $this;
    }

    /**
     * @return $this
     */
    public function success()
    {
        $this->type = 'success';

        return $this;
    }

    /**
     * @return $this
     */
    public function danger()
    {
        $this->type = 'danger';

        return $this;
    }

    /**
     * @return $this
     */
    public function warning()
    {
        $this->type = 'warning';

        return $this;
    }

    /**
     * @return $this
     */
    public function info()
    {
        $this->type = 'info';

        return $this;
    }

    /**
     * @return $this
     */
    public function light()
    {
        $this->type = 'light';

        return $this;
    }

    /**
     * @return $this
     */
    public function dark()
    {
        $this->type = 'dark';

        return $this;
    }
}
