<?php

declare(strict_types=1);

namespace Admin\Components;

use Closure;
use Admin\Traits\FontAwesome;
use Admin\Traits\TypesTrait;

class AccordionBodyComponent extends Component
{
    use TypesTrait;

    /**
     * @var string
     */
    protected string $view = 'accordion.body';

    /**
     * @var string
     */
    protected string $title = "";

    /**
     * @var bool
     */
    protected bool $show = false;

    /**
     * @var int
     */
    protected static int $count = 0;

    /**
     * @var string
     */
    protected string $id = "";

    /**
     * @var string
     */
    protected string $parentId = "";

    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
        $this->id = "collapse_" . static::$count;
        static::$count++;
    }

    /**
     * @param  string  $title
     * @return $this
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param  string  $id
     * @return $this
     */
    public function parentId(string $id): static
    {
        $this->parentId = $id;

        return $this;
    }

    /**
     * @return $this
     */
    public function show(): static
    {
        $this->show = true;

        return $this;
    }

    /**
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
     * @return void
     */
    protected function mount(): void
    {

    }
}
