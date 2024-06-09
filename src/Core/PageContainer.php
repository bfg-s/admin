<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\Components\AccessDeniedComponent;
use Admin\Components\ButtonsComponent;
use Admin\Components\Component;
use Admin\Components\PageComponents;
use Admin\Components\TabsComponent;
use Admin\Explanation;
use Admin\Interfaces\SegmentContainerInterface;
use Admin\Middlewares\Authenticate;
use Admin\Traits\Delegable;
use Admin\Traits\FontAwesomeTrait;
use BadMethodCallException;
use Closure;
use Exception;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\View;

/**
 * The part of the kernel that is responsible for the page container.
 *
 * @mixin PageComponents
 */
abstract class PageContainer implements SegmentContainerInterface
{
    use FontAwesomeTrait;
    use Conditionable;
    use Delegable;

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
     * Explanations of what should be done first after creating a component on a page.
     *
     * @var mixed|array|null
     */
    protected mixed $firstExplanation = null;

    /**
     * Page button groups.
     *
     * @var array
     */
    protected array $buttonGroups = [];

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
     * Create a tab and a tab component if it does not already exist in the latest content.
     *
     * @param ...$delegates
     * @return $this|TabsComponent
     */
    public function tab(...$delegates): static|TabsComponent
    {
        $last = $this->last();

        $tabs = $last instanceof TabsComponent ? $last : $this->tabs();

        $tabs->tab(...$delegates);

        return $this;
    }

    /**
     * Get the latest content component.
     *
     * @return mixed|null
     */
    public function last(): mixed
    {
        if (count($this->contents)) {

            return $this->contents[array_key_last($this->contents)] ?? null;
        }
        return null;
    }

    /**
     * Apply a data collection to a component.
     *
     * @param $collection
     * @param  callable  $callback
     * @return $this
     */
    public function withCollection($collection, callable $callback): static
    {
        foreach ($collection as $key => $item) {
            $result = call_user_func($callback, $item, $key);
            if ($result && is_array($result)) {
                $this->explainForce(
                    Explanation::new(...$result)
                );
            } else if ($result instanceof Delegate) {
                $this->explainForce(
                    Explanation::new(...$result->methods)
                );
            }
        }

        return $this;
    }

    /**
     * A magic method that is responsible for filling this page with content when we magically write the name of our component.
     *
     * @param $name
     * @param $arguments
     * @return static
     * @throws Exception|Throwable
     */
    public function __call($name, $arguments)
    {
        if (isset(Component::$components[$name])) {
            $component = Component::$components[$name];

            /*** @var Component $component * */
            $component = new $component(...$arguments);

            $component->model($this->model);

            if (!$component instanceof Component) {
                throw new Exception('Component is not admin part');
            }

            if ($this->firstExplanation && $name == 'card') {
                $component->explain(call_user_func($this->firstExplanation));
                $this->firstExplanation = null;
            }

            $this->contents[] = $component;
        } elseif (str_ends_with($name, '_by_default')) {
            $name = str_replace('_by_default', '', $name);
            if (!request()->has('method') || request('method') == $name) {
                $this->registerClass($this->{$name}());
                $this->explainForClasses($arguments);
            }
        } elseif (str_ends_with($name, '_by_request')) {
            $name = str_replace('_by_request', '', $name);
            if (request()->has('method') && request('method') == $name) {
                $this->registerClass($this->{$name}());
                $this->explainForClasses($arguments);
            }
        } else {
            if (!static::hasMacro($name)) {
                throw new BadMethodCallException(sprintf(
                    'Method %s::%s does not exist.',
                    static::class,
                    $name
                ));
            }
            $macro = self::$macros[$name];
            if ($macro instanceof Closure) {
                return call_user_func_array($macro->bindTo($this, self::class), $arguments);
            }

            $macro(...$arguments);
        }

        return $this;
    }

    /**
     * Add new buttons group to the page.
     *
     * @param ...$delegates
     * @return $this
     */
    public function buttons(...$delegates): static
    {
        $this->buttonGroups[] = ButtonsComponent::create(...$delegates);

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
            'buttonGroups' => $this->buttonGroups,
        ]);
    }
}
