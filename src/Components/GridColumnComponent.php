<?php

namespace Admin\Components;

use Admin\Explanation;

class GridColumnComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'grid-column';

    /**
     * @var int|null
     */
    protected ?int $num = null;

    /**
     * @param  array  $delegates
     */
    public function __construct(int $num = null, ...$delegates)
    {
        parent::__construct();

        $this->explainForce(Explanation::new($delegates));

        if ($num) {
            $this->num($num);
        }
    }

    /**
     * @param  int  $num
     * @return $this
     */
    public function num(int $num): static
    {
        $this->num = $num;

        return $this;
    }

    /**
     * @return int[]|null[]
     */
    protected function viewData(): array
    {
        return [
            'num' => $this->num
        ];
    }

    /**
     * @return void
     */
    protected function mount(): void
    {

    }
}
