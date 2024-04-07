<?php

declare(strict_types=1);

namespace Admin\Components\Small;

use Admin\Components\Component;

class AComponent extends Component
{
    /**
     * @var string
     */
    protected $element = 'a';

    /**
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }

    /**
     * @param  string  $href
     * @return $this
     */
    public function setHref(string $href): static
    {
        $this->attr('href', $href);

        return  $this;
    }
}
