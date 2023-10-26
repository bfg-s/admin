<?php

namespace Admin\Components;

class LiveComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'live';

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

        $this->forceDelegates(...$delegates);
    }

    /**
     * @return string[]
     */
    protected function viewData(): array
    {
        $id = 'live-'.static::$counter;

        LiveComponent::$list[$id] = $this;

        static::$counter++;

        return [
            'id' => $id
        ];
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
    }
}
