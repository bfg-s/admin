<?php

namespace Admin\Repositories;

use Bfg\Repository\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Admin\Core\MenuItem;
use Admin\Exceptions\ShouldBeModelInControllerException;
use Admin\Models\AdminPermission;
use Admin\Models\AdminUser;
use Navigate;
use Route;

/**
 * @property-read string|null $currentQueryField
 * @property-read string|null $type
 * @property-read array $getCurrentQuery
 * @property-read MenuItem $now
 * @property-read array|null $data
 * @property-read mixed $modelPrimary
 * @property-read mixed $currentController
 * @property-read Collection $nowParents
 * @property-read Model $modelNow
 * @property-read null $saveCurrentQuery
 * @property-read bool $isDarkMode
 * @property-read Collection|MenuItem[] $menuList
 */
class AdminRepository extends Repository
{
    /**
     * @var array
     */
    protected static array $cache = [
        'models' => [],
        'queries' => [],
        'menu_item_counter' => 1,
        'nested_counter' => 0,
    ];

    /**
     * @return mixed
     */
    public function now(): mixed
    {
        $return = $this->menuList->where('route', '=', $this->currentQueryField)->first();
        if (!$return) {
            $route = preg_replace('/\.[a-zA-Z0-9_\-]+$/', '', $this->currentQueryField);
            $return = $this->menuList->where('route', '=', $route)->first();
        }
        return $return;
    }

    /**
     * @return string|null
     */
    public function type(): ?string
    {
        $return = null;

        $menu = $this->now;

        if ($menu && $menu->isResource()) {
            $return = $menu->getType();

            if ($return === 'store') {
                $return = 'create';
            } elseif ($return === 'update') {
                $return = 'edit';
            }
        }

        return $return;
    }

    /**
     * @param $__name_
     * @param $__default
     * @return array|mixed|null
     */
    public function data($__name_ = null, $__default = null): mixed
    {
        $return = $__default;

        $menu = $this->now;

        if ($menu && $menu->isResource()) {
            $return = $menu->getData();

            if ($__name_) {
                $return = $return[$__name_] ?? $__default;
            }
        }

        return $return;
    }

    /**
     * @return object|string|null
     */
    public function modelPrimary(): object|string|null
    {
        $menu = $this->now;

        if (Route::current() && $menu->getResourceRoute()) {
            return Route::current()->parameter(
                $menu->getResourceRoute()
            );
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     * @throws ShouldBeModelInControllerException
     */
    public function modelNow(): mixed
    {
        $return = null;

        if ($controller = $this->currentController) {
            if (method_exists($controller, 'getModel')) {
                $return = call_user_func([$controller, 'getModel']);
            } elseif (property_exists($controller, 'model')) {
                $return = $controller::$model;
            } else {
                throw new ShouldBeModelInControllerException();
            }
        }

        if (is_string($return) && class_exists($return)) {
            /** @var Model $return */
            $return = new $return();
        }

        $pm = $this->now?->getResourceRoute() ?? Str::singular(Str::snake(class_basename($return)));

        if (
            $pm &&
            $return instanceof Model &&
            !$return->exists &&
            Route::current()->hasParameter($pm)
        ) {
            if ($find = $return->where($return->getRouteKeyName(), Route::current()->parameter($pm))->first()) {
                $return = $find;
            }
        }

        return $return;
    }

    /**
     * @param  Request  $request
     * @return void
     */
    public function saveCurrentQuery(Request $request): void
    {
        $can = $request->pjax() || !$request->ajax();
        if ($request->isMethod('GET') && $can) {
            $name = $this->currentQueryField;
            if (isset(static::$cache['queries'][$name]) && static::$cache['queries'][$name]) {
                return;
            }
            $all = $request->query();
            foreach ($all as $key => $item) {
                if (str_starts_with($key, '_') || $key == 'format' || $key == 'q') {
                    unset($all[$key]);
                }
            }
            static::$cache['queries'][$name] = $all;
            session([$name => $all]);
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentQuery(): mixed
    {
        return $this->getQuery($this->currentQueryField);
    }

    /**
     * @param  string  $name
     * @return mixed
     */
    public function getQuery(string $name): mixed
    {
        if (!isset(static::$cache['queries'][$name]) || !static::$cache['queries'][$name]) {
            static::$cache['queries'][$name] = session($name, []);
        }

        return static::$cache['queries'][$name];
    }

    /**
     * @return string|null
     */
    public function currentQueryField(): ?string
    {
        return Route::currentRouteName();
    }

    /**
     * @return mixed
     */
    public function currentController(): mixed
    {
        return Route::current()?->controller;
    }

    /**
     * @return Collection
     */
    public function nowParents(): Collection
    {
        return collect($this->now ? $this->getParents($this->now) : []);
    }

    /**
     * @param  MenuItem  $menuItem
     * @param  array  $result
     * @return array
     */
    protected function getParents(MenuItem $menuItem, array $result = []): array
    {
        $result[$menuItem->getId()] = $menuItem;

        if ($menuItem->getParentId()) {
            $parent = $this->menuList
                ->where('active', true)
                ->where('id', $menuItem->getParentId())
                ->first();

            if ($parent) {
                return $this->getParents($parent, $result);
            } else {
                return $result;
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isDarkMode(): bool
    {
        $mode = request()->cookie('admin-dark-mode');
        $mode = $mode
            ? (explode("|", Crypt::decryptString($mode))[1] ?? (int) config('admin.dark_mode', true))
            : (int) config('admin.dark_mode', true);

        return $mode == 1;
    }

    /**
     * @return Collection
     */
    public function menuList(): Collection
    {
        return $this->buildMenuList();
    }

    /**
     * @param  array|null  $items
     * @param  MenuItem|null  $parent
     * @return Collection
     */
    private function buildMenuList(
        ?array $items = null,
        MenuItem $parent = null
    ): Collection {
        $items = $items !== null ? $items : Navigate::getMaked();
        $result = collect();
        foreach ($items as $item) {
            $child = $item['items'] ?? false;
            $menuItem = new MenuItem();
            $result->push($menuItem);

            $menuItem->setId(static::$cache['menu_item_counter']);
            $menuItem->setParentId($parent?->getId() ?: 0);
            $menuItem->setRoute($item['route'] ?? null);
            $menuItem->setTitle($item['title'] ?? null);
            $menuItem->setAction($item['action'] ?? null);
            $menuItem->setActive($item['active'] ?? false);
            $menuItem->setPost($item['post'] ?? false);
            $menuItem->setIcon($item['icon'] ?? null);
            $menuItem->setBadge($item['badge'] ?? null);
            $menuItem->setData($item['data'] ?? null);
            $menuItem->setHeadTitle($item['head_title'] ?? null);
            $menuItem->setNavBarView($item['nav_bar_view'] ?? null);
            $menuItem->setLeftNavBarView($item['left_nav_bar_view'] ?? null);
            $menuItem->setPrepend($item['prepend'] ?? false);
            $menuItem->setExtension($item['extension'] ?? null);
            $menuItem->setMainHeader($item['main_header'] ?? null);
            $menuItem->setResource($item['resource'] ?? null);
            $menuItem->setResourceRoute($item['resource_route'] ?? null);
            $menuItem->setResourceOnly($item['resource_only'] ?? null);
            $menuItem->setResourceExcept($item['resource_except'] ?? null);
            $menuItem->setLinkParams($item['link_params'] ?? null);
            $menuItem->setDontUseSearch($item['dontUseSearch'] ?? null);
            $menuItem->setParams($item['params'] ?? null);
            $menuItem->mergeRoles($item['roles'] ?? []);
            $menuItem->mergeRoles($parent?->getRoles());
            $menuItem->insertParentRouteName($parent?->getRoute() ?: 'admin');
            $menuItem->setCurrentRoute($this->currentQueryField);
            $menuItem->setTarget();
            if ($menuItem->getAction()) {
                $menuItem->mergeRouteParams($this->getQuery($menuItem->getRoute()));
                $menuItem->setLink();
                $menuItem->setController();
                $menuItem->setCurrent($menuItem->getRoute() == $this->currentQueryField);
            } elseif ($menuItem->getResource()) {
                $menuItem->setRouteParams(
                    array_callable_results($menuItem->getRouteParams() ?? [], $menuItem)
                );
                $menuItem->setType(
                    str_replace($menuItem->getRoute().'.', '', $this->currentQueryField)
                );
                $menuItem->setCurrent(
                    str_replace('.'.$menuItem->getType(), '', $this->currentQueryField)
                    == $menuItem->getRoute()
                );
                $menuItem->setLink(
                    $menuItem->getLinkIndex()
                );
                $menuItem->setController(
                    $menuItem->getResourceAction()
                );
            }

            $menuItem->setModelClass();
            $menuItem->setSelected();

            if (!$menuItem->isActive()) {
                $menuItem->setActive(
                    !!$menuItem->getTitle()
                );
            }

            if ($menuItem->isActive() && $menuItem->getLink()) {
                $menuItem->setActive(
                    AdminPermission::checkUrl($menuItem->getLink())
                );
            }

            if ($child) {
                $childList = $this->buildMenuList($child, $menuItem);
                $menuItem->setChild(
                    collect($childList)
                );
                $result->last()->setActive(
                    $menuItem->getChild()->where('active', true)->isNotEmpty()
                );

                $result = $result->merge($childList);
            }

            static::$cache['menu_item_counter']++;
        }
        return $result;
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return AdminUser::class;
    }
}
