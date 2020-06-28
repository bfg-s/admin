<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits;

/**
 * Trait SearchFormHelpersTrait
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait SearchFormHelpersTrait {

    /**
     * @return $this
     */
    public function id()
    {
        $this->numeric('id', 'lte.id', '=');

        return $this;
    }

    /**
     * @return $this
     */
    public function created_at()
    {
        $this->date_time_range('created_at', 'lte.created_at');

        return $this;
    }

    /**
     * @return $this
     */
    public function updated_at()
    {
        $this->date_time_range('updated_at', 'lte.updated_at');

        return $this;
    }

    /**
     * @return $this
     */
    public function at()
    {
        return $this->updated_at()->created_at();
    }
}