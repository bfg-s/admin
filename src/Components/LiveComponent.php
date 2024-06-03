<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Live component for dynamic content.
 */
class LiveComponent extends Component
{
    /**
     * List of live fields for access from the system controller.
     *
     * @var array|LiveComponent[]
     */
    public static array $list = [];

    /**
     * Counter of live components for a unique ID.
     *
     * @var int
     */
    protected static int $counter = 0;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'live';

    /**
     * Live component identifier.
     *
     * @var mixed|null
     */
    protected mixed $id = null;

    /**
     * LiveComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->force_delegates = $delegates;
    }

    /**
     * Method to override, used to add events to the component after rendering.
     *
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
     * Additional data to be sent to the template.
     *
     * @return string[]
     */
    protected function viewData(): array
    {
        return [
            'id' => $this->id
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        $this->id = 'live-'.static::$counter;

        LiveComponent::$list[$this->id] = $this;

        static::$counter++;
    }
}
