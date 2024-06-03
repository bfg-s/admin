<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\Typeable;

/**
 * The component that is responsible for the body of the accordion.
 */
class AccordionBodyComponent extends Component
{
    use Typeable;

    /**
     * Accordion body counter for unique identifier.
     *
     * @var int
     */
    protected static int $count = 0;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'accordion.body';

    /**
     * Accordion body title.
     *
     * @var string
     */
    protected string $title = "";

    /**
     * Shown or hidden accordion by default.
     *
     * @var bool
     */
    protected bool $show = false;

    /**
     * Unique identifier of the accordion body.
     *
     * @var string
     */
    protected string $id = "";

    /**
     * The parent id of the accordion body.
     *
     * @var string
     */
    protected string $parentId = "";

    /**
     * AccordionBodyComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
        $this->id = "collapse_".static::$count;
        static::$count++;
    }

    /**
     * Set the title of the accordion body.
     *
     * @param  string  $title
     * @return $this
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the parent id of the accordion body.
     *
     * @param  string  $id
     * @return $this
     */
    public function parentId(string $id): static
    {
        $this->parentId = $id;

        return $this;
    }

    /**
     * Make the body show by default.
     *
     * @return $this
     */
    public function show(): static
    {
        $this->show = true;

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
            'title' => $this->title,
            'type' => $this->type,
            'show' => $this->show,
            'id' => $this->id,
            'parentId' => $this->parentId,
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
