<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\FontAwesome;
use Lar\LteAdmin\Interfaces\SegmentContainerInterface;

/**
 * Class Container
 * @package App\LteAdmin\Segments
 */
class Container implements SegmentContainerInterface {

    use FontAwesome;

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
     * @param  \Closure  $warp
     */
    public function __construct(\Closure $warp)
    {
        $this->layout = 'lte::page';
        $this->component = DIV::create()->only_content();
        $warp($this->component, $this);
        $this->warp = $warp;
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
        return view('lte::wrapper.container', [
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