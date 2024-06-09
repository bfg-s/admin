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
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'small.i';

    /**
     * Element icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * IComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
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
     * Additional data to be sent to the template.
     *
     * @return null[]|string[]
     */
    protected function viewData(): array
    {
        return [
            'icon' => $this->icon,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {

    }
}
