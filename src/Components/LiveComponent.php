<?php

namespace Lar\LteAdmin\Components;

class LiveComponent extends Component
{
    /**
     * @var int
     */
    protected static $counter = 0;

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->forceDelegates(...$delegates);

        $this->addClass('__live__')
            ->setId('live-'.static::$counter);

        static::$counter++;
    }

    protected function mount()
    {
    }
}
