<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\Components\AccessDeniedComponent;
use Admin\Components\ButtonsComponent;
use Admin\Components\Component;
use Admin\Components\PageComponents;
use Admin\Components\TabsComponent;
use Admin\Explanation;
use Admin\Facades\Admin;
use Admin\Interfaces\SegmentContainerInterface;
use Admin\Middlewares\Authenticate;
use Admin\Repositories\AdminRepository;
use Admin\Traits\Delegable;
use Admin\Traits\FontAwesomeTrait;
use BadMethodCallException;
use Closure;
use Exception;
use Illuminate\Support\Collection;
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
     * @var string|null
     */
    protected string|null $pageTitle = null;

    /**
     * Page icon.
     *
     * @var string|null
     */
    protected string|null $pageIcon = null;

    /**
     * Page breadcrumbs.
     *
     * @var array
     */
    protected array $breadcrumbs = [];

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

        $this->breadcrumb(config('app.name'));
    }

    /**
     * Place breadcrumbs on the page.
     *
     * @param  string  $title
     * @param  string|null  $url
     * @return $this
     */
    public function breadcrumb(string $title, string|null $url = null): static
    {
        $this->breadcrumbs[] = compact('title', 'url');

        return $this;
    }

    /**
     * Set a title on the page.
     *
     * @param  string  $title
     * @return $this
     */
    public function title(string $title): static
    {
        $this->pageTitle = $title;

        return $this;
    }

    /**
     * Set an icon to the pages.
     *
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon): static
    {
        $this->pageIcon = $icon;

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
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function render(): View|\Illuminate\Http\JsonResponse
    {
        $nowMenu = admin_repo()->now;

        if (!Authenticate::$access) {
            $this->contents = [
                AccessDeniedComponent::create()
            ];
        }

        if (! $this->pageTitle) {
            $title = $nowMenu ? ($nowMenu->getHeadTitle() ?? ($nowMenu->getTitle() ?? 'Blank page')) : null;
            $this->pageTitle = $nowMenu ? ($title && strtolower($title) !== 'admin' ? __($title) : $title) : null;
        } else {
            $this->pageTitle = strtolower($this->pageTitle) !== 'admin'
                ? __($this->pageTitle)
                : $this->pageTitle;
        }

        if (! $this->pageIcon) {
            $this->pageIcon = $nowMenu ? $nowMenu->getIcon() : null;
        }

        if (count($this->breadcrumbs) === 1) {
            $nowMenuParents = admin_repo()->nowParents;
            foreach ($nowMenuParents->reverse() as $item) {
                $this->breadcrumb($item->getTitle(), $item->getLink());
            }
        }

        $breadcrumbs = array_map(
            fn ($breadcrumb) => array_merge($breadcrumb, [
                'title' => __($breadcrumb['title']),
            ]),
            $this->breadcrumbs
        );

        if (Admin::isApiMode()) {

            return response()->json([
                'meta' => [
                    'pageTitle' => $this->pageTitle,
                    'pageIcon' => $this->pageIcon,
                    'breadcrumbs' => $breadcrumbs,
                ],
                'menu' => admin_repo()->menuList->where('parent_id', 0)->values(),
                'buttonGroups' => $this->upgradeDataToApiResponse($this->buttonGroups),
                'contents' => $this->upgradeDataToApiResponse($this->contents),
            ]);
        }

        return admin_view('container', [
            'contents' => $this->contents,
            'pageTitle' => $this->pageTitle,
            'pageIcon' => $this->pageIcon,
            'breadcrumbs' => $breadcrumbs,
            'buttonGroups' => $this->buttonGroups,
        ]);
    }

    /**
     * @param  mixed  $collection
     * @return array
     */
    public function upgradeDataToApiResponse(mixed $collection): array
    {
        return collect($collection)->filter(
            fn (mixed $content) => (!$content instanceof Component || !$content->ignoreForApi)
                && ! $content instanceof View
        )->map(
            fn (mixed $content) => $content instanceof Component ? $content->exportToApi() : [$content]
        )->collapse()->filter()->toArray();
    }
}
