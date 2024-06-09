<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Tab component of the admin panel.
 */
class TabsComponent extends Component
{
    /**
     * Tab counter.
     *
     * @var int
     */
    protected static int $counter = 0;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'tabs';

    /**
     * List of tabs.
     *
     * @var array
     */
    protected array $tabs = [];

    /**
     * Left orientation of tabs.
     *
     * @var bool
     */
    protected bool $left = true;

    /**
     * Vertical orientation for tab.
     *
     * @var bool
     */
    protected bool $vertical = true;

    /**
     * Left tab orientation is applied.
     *
     * @var bool
     */
    protected bool $leftApplied = false;

    /**
     * Create tabs from array list.
     *
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
     * Add a new tab.
     *
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
     * Native function for adding a new tab.
     *
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
            $title = $content->title;
            $icon = $content->icon;
            $active = $content->isActive();
            if (!$this->leftApplied) {
                $this->left = $content->left;
                $this->leftApplied = true;
            }
            $this->vertical = $content->isVertical();
        }

        $id = 'tab-'.md5($title).'-'.static::$counter;
        $active = $active === null ? !count($this->tabs) : $active;

        $content = ($content ?? TabContentComponent::create())
            ->id($id)
            ->active($active);

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
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'left' => $this->left,
            'tabs' => $this->tabs,
            'vertical' => $this->vertical,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
