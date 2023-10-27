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
     * @var mixed|null
     */
    protected mixed $id = null;

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
        return [
            'id' => $this->id
        ];
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        $this->id = 'live-'.static::$counter;

        LiveComponent::$list[$this->id] = $this;

        static::$counter++;
    }
}
