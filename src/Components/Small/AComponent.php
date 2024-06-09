<?php

declare(strict_types=1);

namespace Admin\Components\Small;

use Admin\Components\Component;

/**
 * The HTML component of the "a" tag.
 */
class AComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'small.a';

    /**
     * Is the tag editable.
     *
     * @var bool
     */
    protected bool $editable = false;

    /**
     * Make the tag editable.
     *
     * @return $this
     */
    public function editable(): static
    {
        $this->editable = true;

        return $this;
    }

    /**
     * Set the source for the tag.
     *
     * @param  string  $href
     * @return $this
     */
    public function setHref(string $href): static
    {
        $this->attr('href', $href);

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'editable' => $this->editable,
        ];
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
