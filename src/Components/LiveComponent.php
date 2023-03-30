<?php

namespace Admin\Components;

class LiveComponent extends Component
{
    /**
     * @var array|LiveComponent[]
     */
    public static array $list = [];

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

        $id = 'live-'.static::$counter;

        LiveComponent::$list[$id] = $this;

        $this->forceDelegates(...$delegates);

        $this->addClass('__live__')
            ->setId($id);


        static::$counter++;
    }

    protected function mount()
    {
    }
}
