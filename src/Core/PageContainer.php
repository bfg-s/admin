<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\Components\AccessDeniedComponent;
use Admin\Components\Component;
use Admin\Interfaces\SegmentContainerInterface;
use Admin\Middlewares\Authenticate;
use Admin\Traits\FontAwesomeTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\View;
use Throwable;

/**
 * The part of the kernel that is responsible for the page container.
 */
abstract class PageContainer implements SegmentContainerInterface
{
    use FontAwesomeTrait;
    use Conditionable;

    /**
     * Page title.
     *
     * @var array
     */
    protected array $pageTitle = [];

    /**
     * Page breadcrumbs.
     *
     * @var array
     */
    protected array $breadcrumb = [];

    /**
     * Page contents.
     *
     * @var array
     */
    protected array $contents = [];

    /**
     * PageContainer constructor.
     *
     * @param  callable|null  $warp
     * @throws \Throwable
     */
    public function __construct(callable $warp = null)
    {
        if (is_embedded_call($warp)) {
            embedded_call($warp, [
                static::class => $this,
            ]);
        }
    }

    /**
     * Place breadcrumbs on the page.
     *
     * @param  mixed|string[]  ...$breadcrumbs
     * @return $this
     */
    public function breadcrumb(...$breadcrumbs): static
    {
        $this->breadcrumb = array_merge($this->breadcrumb, $breadcrumbs);

        return $this;
    }

    /**
     * Set a title and icon on the page.
     *
     * @param  string  $title
     * @param  string|null  $icon
     * @return $this
     */
    public function title(string $title, string $icon = null): static
    {
        if (!$this->pageTitle) {
            $this->pageTitle = ['title' => $title];
        } else {
            $this->pageTitle['title'] = $title;
        }

        if ($icon) {
            $this->pageTitle['icon'] = $icon;
        }

        return $this;
    }

    /**
     * Set an icon and title to the pages.
     *
     * @param  string  $icon
     * @param  string|null  $title
     * @return $this
     */
    public function icon(string $icon, string $title = null): static
    {
        if (!$this->pageTitle) {
            $this->pageTitle = ['icon' => $icon];
        } else {
            $this->pageTitle['icon'] = $icon;
        }

        if ($title) {
            $this->pageTitle['title'] = $title;
        }

        return $this;
    }

    /**
     * Add a template to display on the page.
     *
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
     * Render the current page container.
     *
     * @return View
     */
    public function render(): View
    {
        if (!Authenticate::$access) {
            $this->contents = [
                AccessDeniedComponent::create()
            ];
        }

        return admin_view('container', [
            'contents' => $this->contents,
            'page_info' => $this->pageTitle,
            'breadcrumb' => $this->breadcrumb,
        ]);
    }
}
