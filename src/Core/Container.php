<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\Components\AccessDeniedComponent;
use Admin\Components\Component;
use Admin\Middlewares\Authenticate;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;
use Admin\Interfaces\SegmentContainerInterface;
use Admin\Traits\FontAwesome;
use Illuminate\View\View;
use Throwable;

abstract class Container implements SegmentContainerInterface
{
    use FontAwesome;
    use Conditionable;

    /**
     * @var Component|null
     */
    public ?Component $component = null;

    /**
     * @var array
     */
    public array $storeList = [];

    /**
     * @var array
     */
    protected array $page_title = [];

    /**
     * @var array
     */
    protected array $breadcrumb = [];

    /**
     * @var callable
     */
    private $warp;

    /**
     * @var array
     */
    protected array $contents = [];

    /**
     * @param $warp
     * @throws Throwable
     */
    public function __construct($warp)
    {
        if (is_embedded_call($warp)) {
            embedded_call($warp, [
                'component' => $this->component,
                static::class => $this,
            ]);
        }
        $this->warp = $warp;
    }

    /**
     * @param  mixed  ...$params
     * @return static
     * @throws Throwable
     */
    public static function create(...$params): static
    {
        return new static(...$params);
    }

    /**
     * @param  string  $store
     * @param $data
     * @return $this
     */
    public function toStore(string $store, $data): static
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
    public function breadcrumb(...$breadcrumbs): static
    {
        $this->breadcrumb = array_merge($this->breadcrumb, $breadcrumbs);

        return $this;
    }

    /**
     * @param  string  $title
     * @param  string|null  $icon
     * @return $this
     */
    public function title(string $title, string $icon = null): static
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
    public function icon(string $icon, string $title = null): static
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
     * @param $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return $this
     */
    public function view($view = null, array $data = [], array $mergeData = []): static
    {
        $this->contents[] = admin_view($view, $data, $mergeData);

        return $this;
    }

    /**
     * @return View
     */
    public function render(): View
    {
        if (! Authenticate::$access) {
            $this->contents = [
                AccessDeniedComponent::create()
            ];
        }

        return admin_view('container', [
            'contents' => $this->contents,
            'page_info' => $this->page_title,
            'breadcrumb' => $this->breadcrumb,
            'storeList' => $this->storeList,
        ]);
    }
}
