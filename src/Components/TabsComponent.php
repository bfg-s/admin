<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\Delegable;

class TabsComponent extends Component
{
    use Delegable;

    /**
     * @var string
     */
    protected string $view = 'tabs';

    /**
     * Count of tabs.
     * @var int
     */
    protected static int $counter = 0;

    /**
     * @var array
     */
    protected array $tabs = [];

    /**
     * @var bool
     */
    protected bool $left = true;

    /**
     * @var bool
     */
    protected bool $leftSeted = false;

    /**
     * Tabs constructor.
     * @param  mixed  ...$explanations
     */
    public function __construct(...$explanations)
    {
        parent::__construct(...$explanations);
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'left' => $this->left,
            'tabs' => $this->tabs,
        ];
    }

    /**
     * Create tab from classes.
     * @param  array  $list
     * @return $this
     */
    public function tabList(array $list): static
    {
        foreach ($list as $item) {
            $this->tab($item);
        }

        return $this;
    }

    /**
     * @param ...$delegates
     * @return TabsComponent
     */
    public function tab(...$delegates): static
    {
        $this->createNewTab(
            TabContentComponent::create()->delegatesNow($delegates)
        );

        return $this;
    }

    /**
     * @param  string|TabContentComponent  $title
     * @param $icon
     * @param  callable|array|null  $contentCb
     * @param  bool|null  $active
     * @return Component|TabContentComponent
     */
    public function createNewTab(
        string|TabContentComponent $title,
        $icon = null,
        callable|array $contentCb = null,
        ?bool $active = null
    ): TabContentComponent|Component {
        if ($icon && !is_string($icon)) {
            $contentCb = $icon;
            $icon = null;
        }

        if ($title instanceof TabContentComponent) {
            $content = $title;
            $title = $content->getTitle;
            $icon = $content->getIcon;
            $active = $content->getActiveCondition;
            if (! $this->leftSeted) {
                $this->left = $content->getLeft;
                $this->leftSeted = true;
            }
        }


        $id = 'tab-'.md5($title).'-'.static::$counter;
        $active = $active === null ? !count($this->tabs) : $active;

        $content = ($content ?? TabContentComponent::create())->attr([
            'id' => $id,
            'aria-labelledby' => $id.'-label',
        ])->addClassIf($active, 'active show');

        if (is_callable($contentCb)) {
            call_user_func($contentCb, $content);
        } elseif (is_array($contentCb)) {
            $content->delegates(...$contentCb);
        }

        $this->tabs[] = [
            'id' => $id,
            'active' => $active,
            'icon' => $icon,
            'title' => $title,
            'content' => $content,
        ];

        static::$counter++;

        return $content;
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
