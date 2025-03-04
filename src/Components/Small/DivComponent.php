<?php

declare(strict_types=1);

namespace Admin\Components\Small;

use Admin\Components\Component;

/**
 * The HTML component of the "div" tag.
 */
class DivComponent extends Component
{
    /**
     * DivComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->forceDelegates(...$delegates);
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
