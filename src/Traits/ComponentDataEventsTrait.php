<?php

namespace Admin\Traits;

use Admin\Respond;

/**
 * A trait helper that adds methods for adding event date attributes.
 */
trait ComponentDataEventsTrait
{
    /**
     * Add click date attribute.
     *
     * @param  string|null  $command
     * @param  array  $props
     * @return Respond
     */
    public function dataClick(string $command = null, array $props = []): Respond
    {
        if ($this->hasAttribute('data-click')) {
            $respond = $this->getAttribute('data-click');
        } else {
            $respond = new Respond();
        }


        if ($command) {
            $respond->put($command, $props);
        }

        $this->attr('data-click', $respond);

        return $respond;
    }

    /**
     * Add the initialization date attribute.
     *
     * @param  string|null  $command
     * @param  array  $props
     * @return Respond
     */
    public function dataInit(string $command = null, array $props = []): Respond
    {
        if ($this->hasAttribute('data-init')) {
            $respond = $this->getAttribute('data-init');
        } else {
            $respond = new Respond();
        }


        if ($command) {
            $respond->put($command, $props);
        }

        $this->attr('data-init', $respond);

        return $respond;
    }

    /**
     * Add load date attribute.
     *
     * @param  string|null  $command
     * @param  array  $props
     * @return Respond
     */
    public function dataLoad(string $command = null, array $props = []): Respond
    {
        if ($this->hasAttribute('data-load')) {
            $respond = $this->getAttribute('data-load');
        } else {
            $respond = new Respond();
        }


        if ($command) {

            $respond->put($command, $props);
        }

        $this->attr('data-load', $respond);

        return $respond;
    }
}
