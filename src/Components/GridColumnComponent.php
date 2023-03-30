<?php

namespace Admin\Components;

use Admin\Explanation;

class GridColumnComponent extends Component
{
    /**
     * @var string
     */
    protected $classInner = 'pl-0 col-md';

    /**
     * @param  array  $delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->explainForce(Explanation::new($delegates));
    }

    public function num(int $num)
    {
        $this->classInner .= "-{$num}";

        return $this;
    }

    protected function mount()
    {
        $this->addClass($this->classInner);
    }
}
