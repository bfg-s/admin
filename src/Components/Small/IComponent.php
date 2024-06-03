<?php

declare(strict_types=1);

namespace Admin\Components\Small;

use Admin\Components\Component;
use Admin\Traits\FontAwesomeTrait;

/**
 * The HTML component of the "i" tag.
 */
class IComponent extends Component
{
    use FontAwesomeTrait;

    /**
     * The tag element from which the component begins.
     *
     * @var string
     */
    protected string $element = 'i';

    /**
     * Element icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * IComponent constructor.
     *
     * @param  array  $classes
     * @param ...$delegates
     */
    public function __construct(array $classes = [], ...$delegates)
    {
        parent::__construct($delegates);

        $this->addClass(...$classes);
    }

    /**
     * Set the element icon.
     *
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        if ($this->icon) {
            $this->addClass($this->icon);
        }
    }
}
