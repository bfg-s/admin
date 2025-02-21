<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Explanation;

/**
 * Grid column is a component of the admin panel layout.
 */
class GridColumnComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'grid-column';

    /**
     * Number of columns, maximum 12.
     *
     * @var int|null
     */
    protected ?int $num = null;

    /**
     * Display flex for the column.
     *
     * @var bool
     */
    protected bool $displayFlex = false;

    /**
     * Delegates for the component.
     *
     * @var array
     */
    protected array $delegatesInner = [];

    /**
     * GridColumnComponent constructor.
     *
     * @param  array  $delegates
     */
    public function __construct(int $num = null, ...$delegates)
    {
        parent::__construct();

        $this->delegatesInner = $delegates;

        if ($num) {
            $this->num($num);
        }
    }

    /**
     * Set the column to display flex.
     *
     * @return $this
     */
    public function displayFlex(): static
    {
        $this->displayFlex = true;

        return $this;
    }

    /**
     * Set the number of columns, maximum 12.
     *
     * @param  int  $num
     * @return $this
     */
    public function num(int $num): static
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return int[]|null[]
     */
    protected function viewData(): array
    {
        return [
            'num' => $this->num,
            'displayFlex' => $this->displayFlex,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        $this->explainForce(Explanation::new($this->delegatesInner));
    }
}
