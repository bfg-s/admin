<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Tagable\Events\onRender;

/**
 * @mixin TabsComponentMacroList
 */
class TabsComponent extends DIV implements onRender
{
    use Macroable, Delegable;

    /**
     * Count of tabs.
     * @var int
     */
    protected static $counter = 0;

    /**
     * @var bool
     */
    protected $only_content = true;

    /**
     * @var Component
     */
    protected $nav;

    /**
     * @var Component
     */
    protected $tab_contents;

    /**
     * @var array
     */
    protected $tab_content_props = [];

    /**
     * Tabs constructor.
     * @param  mixed  ...$params
     */
    public function __construct(...$params)
    {
        parent::__construct();

        $this->when($params);

        $this->callConstructEvents();
    }

    /**
     * Create tab from classes.
     * @param  array  $list
     * @return $this
     */
    public function tabList(array $list)
    {
        foreach ($list as $item) {
            $this->tab($item);
        }

        return $this;
    }

    public function tab(...$delegates)
    {
        return $this->createNewTab(
            TabContentComponent::create()->delegatesNow($delegates)
        );
    }

    /**
     * @param  string|TabContentComponent  $title
     * @param $icon
     * @param  callable|array|null  $contentCb
     * @param  bool|null  $active
     * @return Component|\Lar\Layout\LarDoc|TabContentComponent
     */
    public function createNewTab(string|TabContentComponent $title, $icon = null, callable | array $contentCb = null, ?bool $active = null)
    {
        if ($icon && ! is_string($icon)) {
            $contentCb = $icon;
            $icon = null;
        }

        $left = true;

        if ($title instanceof TabContentComponent) {
            $content = $title;
            $title = $content->getTitle;
            $icon = $content->getIcon;
            $active = $content->getActiveCondition;
            $left = $content->getLeft;
        }
        $this->makeNav($left);
        $id = 'tab-'.md5($title).'-'.static::$counter;
        $active = $active === null ? ! $this->nav->contentCount() : $active;
        $a = $this->nav->li(['nav-item'])->a([
            'nav-link',
            'id' => $id.'-label',
            'data-toggle' => 'pill',
            'role' => 'tab',
            'aria-controls' => $id,
            'aria-selected' => $active ? 'true' : 'false',
        ])
            //->on_click('tabs::tab_button')
            ->addClassIf($active, 'active')
            ->setHref("#{$id}");

        if ($icon) {
            $a->i([$icon, 'mr-1']);
        }

        $a->text(__($title));

        $content = (isset($content) ? $content : TabContentComponent::create())->attr([
            'id' => $id,
            'aria-labelledby' => $id.'-label',
        ])->when($this->tab_content_props)
            ->addClassIf($active, 'active show');

        if (is_callable($contentCb)) {
            call_user_func($contentCb, $content);
        } elseif (is_array($contentCb)) {
            $content->delegates(...$contentCb);
        }

        $this->tab_contents
            ->appEnd($content->render());

        static::$counter++;

        return $content;
    }

    /**
     * @param  mixed  ...$props
     * @return $this
     */
    public function container(...$props)
    {
        $this->tab_content_props = array_merge($this->tab_content_props, $props);

        return $this;
    }

    /**
     * @return $this
     */
    protected function makeNav($left)
    {
        if (! $this->nav) {
            $row = $this->div(['row']);
            if ($left) {
                $this->nav = $row->div(['col-md-2'])->ul(['nav flex-column nav-tabs h-100', 'role' => 'tablist', 'aria-orientation' => 'vertical']);
                $this->tab_contents = $row->div(['col-md-10'])->div(['tab-content']);
            } else {
                $this->tab_contents = $row->div(['col-md-10'])->div(['tab-content']);
                $this->nav = $row->div(['col-md-2'])->ul(['nav flex-column nav-tabs nav-tabs-right h-100', 'role' => 'tablist', 'aria-orientation' => 'vertical']);
            }
        }

        return $this;
    }

    /**
     * @return mixed|void
     * @throws \ReflectionException
     */
    public function onRender()
    {
        $this->callRenderEvents();
    }
}
