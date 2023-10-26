<?php

namespace Admin\Traits;

trait DateControlTrait
{
    /**
     * @param  string  $format
     * @return $this
     */
    public function format(string $format): static
    {
        $this->data['format'] = $format;

        return $this;
    }
}
