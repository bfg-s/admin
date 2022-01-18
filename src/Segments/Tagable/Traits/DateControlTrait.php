<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits;

/**
 * Trait DateControlTrait.
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait DateControlTrait
{
    /**
     * @param  string  $format
     * @return $this
     */
    public function format(string $format)
    {
        $this->data['format'] = $format;

        return $this;
    }
}
