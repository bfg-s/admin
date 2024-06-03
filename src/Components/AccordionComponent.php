<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * The component that displays the accordion.
 */
class AccordionComponent extends Component
{
    /**
     * Accordion counter for unique identifier.
     *
     * @var int
     */
    protected static int $count = 0;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'accordion';

    /**
     * Unique identifier of the accordion.
     *
     * @var string
     */
    protected string $id = "";

    /**
     * AccordionComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
        $this->id = "accordion_".static::$count;
        static::$count++;
    }

    /**
     * Add a new body for the accordion.
     *
     * @param  string  $title
     * @return AccordionBodyComponent
     */
    public function body(string $title): AccordionBodyComponent
    {
        $body = $this->createComponent(AccordionBodyComponent::class)->title($title);

        if (!$this->contents) {
            $body->show();
        }

        $body->parentId($this->id);

        $this->appEnd($body);

        return $body;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'count' => static::$count,
            'id' => $this->id,
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
