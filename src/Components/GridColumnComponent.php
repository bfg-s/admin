<?php

namespace LteAdmin\Components;

use Exception;
use Lar\Layout\Tags\DIV;
use Lar\Tagable\Events\onRender;
use Lar\Tagable\Tag;
use LteAdmin\Explanation;
use LteAdmin\Traits\BuildHelperTrait;
use LteAdmin\Traits\Delegable;
use LteAdmin\Traits\FieldMassControlTrait;
use LteAdmin\Traits\Macroable;

class GridColumnComponent extends Component
{
    /**
     * @var string
     */
    protected $class = 'pl-0 col-md';

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
        $this->class .= "-{$num}";

        return $this;
    }

    protected function mount()
    {

    }
}
