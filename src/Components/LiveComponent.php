<?php

declare(strict_types=1);

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

        $this->force_delegates = $delegates;
    }

    /**
     * @param $renderedView
     * @return string
     */
    protected function afterRenderEvent($renderedView): string
    {
        $pattern = '/<div[^>]*>(.*)<\/div>\s*<\/div>/s';
        preg_match($pattern, $renderedView, $matches);
        $contentInsideDiv = $matches[1] ?? '';
        $pattern = '/<div([^>]*)>/';
        $replacement = '<div$1 data-hash="'.sha1($contentInsideDiv).'">';
        return preg_replace($pattern, $replacement, $renderedView, 1);
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
