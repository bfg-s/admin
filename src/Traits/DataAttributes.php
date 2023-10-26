<?php

namespace Admin\Traits;

use Admin\Respond;

trait DataAttributes
{
    /**
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
            //$respond->renderWithExecutor();
        }


        if ($command) {
            $respond->put($command, $props);
        }

        $this->attr('data-click', $respond);

        return $respond;
    }

    /**
     * @param  string|null  $command
     * @param  array  $props
     * @return Respond
     */
    public function dataInit(string $command = null, array $props = []): Respond
    {
        if ($this->xHas('init')) {
            $respond = $this->xGet('init');
        } else {
            $respond = new Respond();
            $respond->renderWithExecutor();
        }

        if ($command) {
            $respond->put($command, $props);
        }

        $this->xInit($respond);

        return $respond;
    }

    /**
     * @param  string|null  $command
     * @param  array  $props
     * @return Respond
     */
    public function dataLoad(string $command = null, array $props = []): Respond
    {
        if ($this->xHas('load')) {
            $respond = $this->xGet('load');
        } else {
            $respond = new Respond();
            $respond->renderWithExecutor();
        }

        if ($command) {
            $respond->put($command, $props);
        }

        $this->xOnLoad($respond);

        return $respond;
    }
}
