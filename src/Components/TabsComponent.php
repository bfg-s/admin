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

    /**
     * @param  string  $title
     * @param  string|mixed  $icon
     * @param  callable|null  $contentCb
     * @param  bool|null  $active
     * @return Component
     */
    public function tab(string $title, $icon = null, callable $contentCb = null, ?bool $active = null)
    {
        if ($icon && ! is_string($icon)) {
            $contentCb = $icon;
            $icon = null;
        }

        if (class_exists($title)) {
            $content = new $title();

            if ($content instanceof TabContentComponent) {
                if ($content->getTitle()) {
                    $title = $content->getTitle();
                }
                if ($content->getIcon()) {
                    $icon = $content->getIcon();
                }
            } else {
                if (isset($content->title) && $content->title) {
                    $title = $content->title;
                }
                if (isset($content->icon) && $content->icon) {
                    $icon = $content->icon;
                }
            }
        }

        $this->makeNav();
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
            ->when(static function (TabContentComponent $content) use ($contentCb) {
                call_user_func($contentCb, $content);
            })
            ->addClassIf($active, 'active show');

        $this->tab_contents
            ->appEnd($content);

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
    protected function makeNav()
    {
        if (! $this->nav) {
            $this->nav = $this->ul(['nav nav-tabs', 'role' => 'tablist']);
            $this->tab_contents = $this->div(['tab-content']);
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
