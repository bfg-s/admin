<?php

namespace Lar\LteAdmin\Components\Traits;

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
