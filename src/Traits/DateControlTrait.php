<?php

declare(strict_types=1);

namespace Admin\Traits;

/**
 * Trait helper for search form date inputs.
 */
trait DateControlTrait
{
    /**
     * Method that determines the date format.
     *
     * @param  string  $format
     * @return $this
     */
    public function format(string $format): static
    {
        $this->data['format'] = $format;

        return $this;
    }
}
