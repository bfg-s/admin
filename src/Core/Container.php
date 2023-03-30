<?php

namespace Admin\Core;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Admin\Interfaces\SegmentContainerInterface;
use Admin\Traits\Eventable;
use Admin\Traits\FontAwesome;
use Admin\Traits\Piplineble;
use Throwable;

abstract class Container implements SegmentContainerInterface
{
    use FontAwesome;
    use Eventable;
    use Piplineble;
    use Conditionable;

    /**
     * @var Component
     */
    public $component;
    /**
     * @var array
     */
    public array $storeList = [];
    /**
     * @var string
     */
    protected $layout;
    /**
     * @var string
     */
    protected $content_yield = 'content';
    /**
     * @var null
     */
    protected $page_title = [];
    /**
     * @var array
     */
    protected $breadcrumb = [];
    /**
     * @var Closure
     */
    private $warp;

    /**
     * @param $warp
     * @throws Throwable
     */
    public function __construct($warp)
    {
        $this->layout = 'admin::layout';
        //$this->component = DIV::create();
        $this->component = DIV::create(['row', 'pl-3 pr-3']);
        $this->callConstructEvents([DIV::class => $this->component]);
        if (is_embedded_call($warp)) {
            embedded_call($warp, [
                DIV::class => $this->component,
                static::class => $this,
            ]);
        }
        $this->warp = $warp;
    }

    /**
     * @param  mixed  ...$params
     * @return static
     */
    public static function create(...$params)
    {
        return new static(...$params);
    }

    public function toStore(string $store, $data)
    {
        if (!isset($this->storeList[$store])) {
            $this->storeList[$store] = [];
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $this->storeList[$store] = array_merge(
            $this->storeList[$store],
            $data
        );

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

        if ($icon) {
            $this->page_title['icon'] = $icon;
        }

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

        if ($title) {
            $this->page_title['title'] = $title;
        }

        return $this;
    }

    /**
     * @return string|void
     */
    public function render()
    {
        return view('admin::container', [
            'layout' => $this->layout,
            'yield' => $this->content_yield,
            'component' => $this->component,
            'page_info' => $this->page_title,
            'breadcrumb' => $this->breadcrumb,
            'storeList' => $this->storeList,
        ]);
    }
}
