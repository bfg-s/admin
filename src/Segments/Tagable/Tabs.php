<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Tagable\Events\onRender;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 */
class Tabs extends DIV implements onRender {

    use Macroable;

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
     * @param  string  $title
     * @param  string|mixed  $icon
     * @param  array  $attrs
     * @return Component
     */
    public function tab(string $title, $icon = null, ...$attrs)
    {
        if($icon && !is_string($icon)) {
            $attrs[] = $icon;
            $icon = null;
        }

        $this->makeNav();
        $id = 'tab-' . md5($title);
        $active = !$this->nav->contentCount();
        $a = $this->nav->li(['nav-item'])->a([
            'nav-link',
            'id' => $id . "-label",
            'data-toggle' => 'pill',
            'role' => 'tab',
            'aria-controls' => $id,
            'aria-selected' => $active ? 'true' : 'false'
        ])
            ->addClassIf($active, 'active')
            ->setHref("#{$id}");

        if ($icon) $a->i([$icon, 'mr-1']);

        $a->text(__($title));

        $content = $this->tab_contents
            ->div([
                'tab-pane p-3',
                'id' => $id,
                'role' => 'tabpanel',
                'aria-labelledby' => $id . "-label"
            ])->when($this->tab_content_props)
            ->when($attrs)
            ->addClassIf($active, 'active show');

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
    protected function makeNav () {

        if (!$this->nav) {

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