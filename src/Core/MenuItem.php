<?php

namespace Admin\Core;

use App;
use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Admin\ExtendProvider;

class MenuItem implements ArrayAccess
{
    protected ?int $id = null; // id
    protected ?int $parent_id = null; // parent_id

    protected ?ExtendProvider $extension = null; // extension
    protected ?Collection $child = null; // child

    protected ?string $title = null; // title
    protected ?string $route = null; // route
    protected ?string $icon = null; // icon
    protected ?string $resource_route = null;
    protected ?string $link = null; // link
    protected ?string $type = null; // current.type
    protected ?string $controller = null; // controller
    protected ?string $model_class = null; // model_class
    protected ?string $current_route = null; // current_route
    protected ?string $post = null; // post
    protected ?string $head_title = null; // head_title
    protected ?string $main_header = null; // main_header
    protected ?string $nav_bar_view = null; // nav_bar_view
    protected ?string $left_nav_bar_view = null; // left_nav_bar_view

    protected array|string|null $action = null; // action
    protected ?array $resource = null; // resource
    protected ?array $roles = null; // roles
    protected ?array $route_params = null; // route_params
    protected ?array $resource_only = null; // resource_only
    protected ?array $resource_except = null; // resource_except
    protected ?array $badge = null; // badge
    protected ?array $data = null; // data
    protected ?array $params = null; // params

    protected mixed $model = null; // model

    protected bool $selected = false; // selected
    protected bool $current = false; // current
    protected bool $target = false; // target
    protected bool $active = false; // active
    protected bool $prepend = false; // prepend

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param  int|null  $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    /**
     * @param  int|null  $parent_id
     */
    public function setParentId(?int $parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return array|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param  array|null  $roles
     */
    public function setRoles(?array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param  array|null  $roles
     */
    public function mergeRoles(?array $roles): void
    {
        $this->roles = array_merge($this->roles ?: [], $roles ?: []);
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param  string|null  $link
     */
    public function setLink(?string $link = null): void
    {
        $this->link = $link === null
            ? route($this->route, $this->route_params)
            : $link;
    }

    /**
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkShow(mixed $params): ?string
    {
        return $this->generateResourceLink('show', $params, true);
    }

    protected function generateResourceLink(string $type, mixed $params, bool $rememberedQuery)
    {
        if (
            ($this->resource_only && !in_array($type, $this->resource_only))
            || ($this->resource_except && in_array($type, $this->resource_except))
        ) {
            return null;
        }
        if (!is_array($params) && $this->resource_route) {
            $params = [$this->resource_route => $params];
        }
        $name = $this->getRoute().'.'.$type;
        if ($rememberedQuery) {
            $params = array_merge($params, session($name, []));
        }

        return route($name, array_merge($params, ($item['route_params'] ?? [])));
    }

    /**
     * @return string|null
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @param  string|null  $route
     */
    public function setRoute(?string $route): void
    {
        $this->route = $route;
    }

    /**
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkUpdate(mixed $params): ?string
    {
        return $this->generateResourceLink('update', $params, false);
    }

    /**
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkDestroy(mixed $params): ?string
    {
        return $this->generateResourceLink('destroy', $params, false);
    }

    /**
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkEdit(mixed $params): ?string
    {
        return $this->generateResourceLink('edit', $params, true);
    }

    /**
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkIndex(mixed $params = []): ?string
    {
        return $this->generateResourceLink('index', $params, true);
    }

    /**
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkCreate(mixed $params = []): ?string
    {
        return $this->generateResourceLink('create', $params, true);
    }

    /**
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkStore(mixed $params = []): ?string
    {
        return $this->generateResourceLink('store', $params, false);
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

    /**
     * @param  bool|null  $selected
     */
    public function setSelected(?bool $selected = null): void
    {
        if ($selected === null) {
            if (!$this->isSelected() && $this->getRoute()) {
                $selected = $this->getRoute() == $this->current_route
                    || Str::is($this->getRoute().'.*', $this->current_route);
            } elseif (!$this->isSelected() && $this->link && !$this->isTarget()) {
                $this->link = trim($this->link, '/');
                $this->link = ltrim($this->link, App::getLocale());
                $this->link = trim($this->link, '/');
                $path = ltrim(request()->decodedPath().'/', App::getLocale());
                $path = trim($path, '/');
                $this->link = '/'.App::getLocale().'/'.$this->link;
                $selected = Str::is($this->link.'*', $path);
            }
        }
        $this->selected = $selected;
    }

    /**
     * @return bool
     */
    public function isTarget(): bool
    {
        return $this->target;
    }

    /**
     * @param  bool|null  $target
     */
    public function setTarget(?bool $target = null): void
    {
        $target = $target === null
            ? str_starts_with($this->link, 'http')
            : $target;

        $this->target = $target;
    }

    /**
     * @return bool
     */
    public function isNotCurrent(): bool
    {
        return !$this->isCurrent();
    }

    /**
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * @param  bool  $current
     */
    public function setCurrent(bool $current): void
    {
        $this->current = $current;
    }

    /**
     * @return bool
     */
    public function isResource(): bool
    {
        return !!$this->type;
    }

    /**
     * @param  string  $type
     * @return bool
     */
    public function isType(string $type): bool
    {
        return $this->type === $type;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param  bool  $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param  string|null  $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param  string|null  $icon
     */
    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return string|null
     */
    public function getResourceRoute(): ?string
    {
        return $this->resource_route;
    }

    /**
     * @param  string|null  $resource_route
     */
    public function setResourceRoute(?string $resource_route): void
    {
        $this->resource_route = $resource_route;
    }

    /**
     * @return array|null
     */
    public function getAction(): ?array
    {
        return $this->action;
    }

    /**
     * @param  array|null  $action
     */
    public function setAction(?array $action): void
    {
        $this->action = $action;
    }

    /**
     * @return array|null
     */
    public function getResource(): ?array
    {
        return $this->resource;
    }

    /**
     * @param  array|null  $resource
     */
    public function setResource(?array $resource): void
    {
        $this->resource = $resource;
    }

    /**
     * @return string|null
     */
    public function getResourceAction(): ?string
    {
        return isset($this->resource['action'])
            ? ltrim($this->resource['action'], '\\')
            : null;
    }

    /**
     * @return mixed|null
     */
    public function getModel(): mixed
    {
        return $this->model;
    }

    /**
     * @param  mixed|null  $model
     */
    public function setModel(mixed $model): void
    {
        $this->model = $model;
    }

    /**
     * @param  callable|null  $link_params
     */
    public function setLinkParams(?callable $link_params): void
    {
        if ($link_params) {
            $this->mergeRouteParams(
                call_user_func($link_params)
            );
        }
    }

    /**
     * @param  array|null  $route_params
     */
    public function mergeRouteParams(?array $route_params): void
    {
        $this->route_params = array_merge($this->route_params ?: [], $route_params ?: []);
    }

    /**
     * @return array|null
     */
    public function getRouteParams(): ?array
    {
        return $this->route_params;
    }

    /**
     * @param  array|null  $route_params
     */
    public function setRouteParams(?array $route_params): void
    {
        $this->route_params = $route_params;
    }

    /**
     * @return string|null
     */
    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * @param  string|null  $controller
     */
    public function setController(?string $controller = null): void
    {
        if (!$controller) {
            $controller = ltrim(
                is_array($this->action) ? $this->action[0] : Str::parseCallback($this->action)[0],
                '\\'
            );
        }
        $this->controller = $controller;
    }

    /**
     * @param  string|null  $routeName
     * @return void
     */
    public function insertParentRouteName(?string $routeName)
    {
        if ($routeName) {
            if (str_replace(['{', '?', '}'], '', $this->route) !== $this->route) {
                $this->setRoute($routeName);
            } else {
                $routePath = $this->getResourceName()
                    ? str_replace('/', '.', $this->getResourceName())
                    : $this->route;
                $this->setRoute($routeName.'.'.$routePath);
            }
        }
    }

    /**
     * @return string|null
     */
    public function getResourceName(): ?string
    {
        return $this->resource['name'] ?? null;
    }

    /**
     * @return ExtendProvider|null
     */
    public function getExtension(): ?ExtendProvider
    {
        return $this->extension;
    }

    /**
     * @param  ExtendProvider|null  $extension
     */
    public function setExtension(?ExtendProvider $extension): void
    {
        $this->extension = $extension;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param  string|null  $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array|null
     */
    public function getResourceOnly(): ?array
    {
        return $this->resource_only;
    }

    /**
     * @param  array|null  $resource_only
     */
    public function setResourceOnly(?array $resource_only): void
    {
        $this->resource_only = $resource_only;
    }

    /**
     * @return array|null
     */
    public function getResourceExcept(): ?array
    {
        return $this->resource_except;
    }

    /**
     * @param  array|null  $resource_except
     */
    public function setResourceExcept(?array $resource_except): void
    {
        $this->resource_except = $resource_except;
    }

    /**
     * @return string|null
     */
    public function getModelClass(): ?string
    {
        return $this->model_class;
    }

    /**
     * @param  string|null  $model_class
     */
    public function setModelClass(?string $model_class = null): void
    {
        if ($model_class === null && $controller = $this->controller) {
            $model_class = $controller::$model ?? null;
        }

        $this->model_class = $model_class;
    }

    /**
     * @return string|null
     */
    public function getCurrentRoute(): ?string
    {
        return $this->current_route;
    }

    /**
     * @param  string|null  $current_route
     */
    public function setCurrentRoute(?string $current_route): void
    {
        $this->current_route = $current_route;
    }

    /**
     * @return Collection|null
     */
    public function getChild(): ?Collection
    {
        return $this->child;
    }

    /**
     * @param  Collection|null  $child
     */
    public function setChild(?Collection $child): void
    {
        $this->child = $child;
    }

    /**
     * @return array|null
     */
    public function getBadge(): ?array
    {
        return $this->badge;
    }

    /**
     * @param  array|null  $badge
     */
    public function setBadge(?array $badge): void
    {
        $this->badge = $badge;
    }

    /**
     * @return string|null
     */
    public function getPost(): ?string
    {
        return $this->post;
    }

    /**
     * @param  string|null  $post
     */
    public function setPost(?string $post): void
    {
        $this->post = $post;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param  array|null  $data
     */
    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getHeadTitle(): ?string
    {
        return $this->head_title;
    }

    /**
     * @param  string|null  $head_title
     */
    public function setHeadTitle(?string $head_title): void
    {
        $this->head_title = $head_title;
    }

    /**
     * @return string|null
     */
    public function getMainHeader(): ?string
    {
        return $this->main_header;
    }

    /**
     * @param  string|null  $main_header
     */
    public function setMainHeader(?string $main_header): void
    {
        $this->main_header = $main_header;
    }

    /**
     * @return bool
     */
    public function isPrepend(): bool
    {
        return $this->prepend;
    }

    /**
     * @param  bool  $prepend
     */
    public function setPrepend(bool $prepend): void
    {
        $this->prepend = $prepend;
    }

    /**
     * @return string|null
     */
    public function getNavBarView(): ?string
    {
        return $this->nav_bar_view;
    }

    /**
     * @param  string|null  $nav_bar_view
     */
    public function setNavBarView(?string $nav_bar_view): void
    {
        $this->nav_bar_view = $nav_bar_view;
    }

    /**
     * @return string|null
     */
    public function getLeftNavBarView(): ?string
    {
        return $this->left_nav_bar_view;
    }

    /**
     * @param  string|null  $left_nav_bar_view
     */
    public function setLeftNavBarView(?string $left_nav_bar_view): void
    {
        $this->left_nav_bar_view = $left_nav_bar_view;
    }

    /**
     * @return array|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @param  array|null  $params
     */
    public function setParams(?array $params): void
    {
        $this->params = $params;
    }


    public function offsetExists(mixed $offset)
    {
        return property_exists($this, $offset);
    }

    public function offsetGet(mixed $offset)
    {
        return $this->{$offset};
    }

    public function offsetSet(mixed $offset, mixed $value)
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset(mixed $offset)
    {
        $this->{$offset} = null;
    }
}
