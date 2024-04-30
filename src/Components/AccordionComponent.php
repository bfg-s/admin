<?php

declare(strict_types=1);

namespace Admin\Components;

class AccordionComponent extends Component
{
    /**
     * @var int
     */
    protected static int $count = 0;

    /**
     * @var string
     */
    protected string $view = 'accordion';

    /**
     * @var string
     */
    protected string $id = "";

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
        $this->id = "accordion_".static::$count;
        static::$count++;
    }

    /**
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
     * @return void
     */
    protected function mount(): void
    {
    }
}
