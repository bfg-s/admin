<?php

declare(strict_types=1);

namespace Admin\Components\Small;

use Admin\Components\Component;
use Admin\Traits\FontAwesome;

class IComponent extends Component
{
    use FontAwesome;

    /**
     * @var string
     */
    protected $element = 'i';

    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @param  array  $classes
     * @param ...$delegates
     */
    public function __construct(array $classes = [], ...$delegates)
    {
        parent::__construct($delegates);

        $this->addClass(...$classes);
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        if ($this->icon) {
            $this->addClass($this->icon);
        }
    }
}
