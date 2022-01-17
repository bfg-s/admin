<?php

namespace Lar\LteAdmin\Segments;

use Illuminate\Support\Traits\Conditionable;
use Lar\Developer\Core\Traits\Eventable;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Traits\FontAwesome;
use Lar\LteAdmin\Interfaces\SegmentContainerInterface;

/**
 * Class Container
 * @package Lar\LteAdmin\Segments
 */
class Container implements SegmentContainerInterface {

    use FontAwesome, Eventable, Piplineble, Conditionable;

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var string
     */
    protected $content_yield = "content";

    /**
     * @var Component
     */
    protected $component;

    /**
     * @var null
     */
    protected $page_title = [];

    /**
     * @var array
     */
    protected $breadcrumb = [];
    /**
     * @var \Closure
     */
    private $warp;

    /**
     * Container constructor.
     * @param  \Closure|array  $warp
     */
    public function __construct($warp)
    {
        $this->layout = 'lte::layout';
        $this->component = DIV::create();
        $this->callConstructEvents([DIV::class => $this->component]);
        if (is_embedded_call($warp)) {
            embedded_call($warp, [
                DIV::class => $this->component,
                static::class => $this
            ]);
        }
        $this->warp = $warp;
    }

    /**
     * Make next component in div
     * @param  \Closure|array  $warp
     * @return $this
     */
    public function next($warp)
    {
        if (is_embedded_call($warp)) {
            embedded_call($warp, [
                DIV::class => $this->component,
                static::class => $this
            ]);
        }

        return $this;
    }

    /**
     * @param  mixed|string[]  ...$breadcrumbs
     * @return $this
     */
    public function breadcrumb(...$breadcrumbs)
    {
        $this->breadcrumb = array_merge($this->breadcrumb, $breadcrumbs);

        return $this;
    }

    /**
     * @param  string  $title
     * @param  string|null  $icon
     * @return $this
     */
    public function title(string $title, string $icon = null)
    {
        if (!$this->page_title) {
            $this->page_title = ['title' => $title];
        } else {
            $this->page_title['title'] = $title;
        }

        if ($icon) { $this->page_title['icon'] = $icon; }

        return $this;
    }

    /**
     * @param  string  $icon
     * @param  string|null  $title
     * @return $this
     */
    public function icon(string $icon, string $title = null)
    {
        if (!$this->page_title) {
            $this->page_title = ['icon' => $icon];
        } else {
            $this->page_title['icon'] = $icon;
        }

        if ($title) { $this->page_title['title'] = $title; }

        return $this;
    }

    /**
     * @return string|void
     */
    public function render()
    {
        $this->callRenderEvents([DIV::class => $this->component]);

        return view('lte::container', [
            'layout' => $this->layout,
            'yield' => $this->content_yield,
            'component' => $this->component,
            'page_info' => $this->page_title,
            'breadcrumb' => $this->breadcrumb
        ]);
    }

    /**
     * @param  mixed  ...$params
     * @return static
     */
    public static function create(...$params)
    {
        return new static(...$params);
    }
}
