<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\ExtendProvider;
use App;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * The part of the kernel that is responsible for the item menu.
 */
class MenuItem implements ArrayAccess, Arrayable
{
    /**
     * Item menu ID.
     *
     * @var int|null id
     */
    protected int|null $id = null;

    /**
     * The parent identifier of the menu item.
     *
     * @var int|null parent_id
     */
    protected int|null $parent_id = null;

    /**
     * Item menu extension provider.
     *
     * @var \Admin\ExtendProvider|null extension
     */
    protected ExtendProvider|null $extension = null;

    /**
     * A collection of built-in children's menu items.
     *
     * @var \Illuminate\Support\Collection|null child
     */
    protected Collection|null $child = null;

    /**
     * Item menu title.
     *
     * @var string|null title
     */
    protected string|null $title = null;

    /**
     * Route menu item.
     *
     * @var string|null route
     */
    protected string|null $route = null;

    /**
     * Item menu icon.
     *
     * @var string|null icon
     */
    protected string|null $icon = null;

    /**
     * Route resource menu item.
     *
     * @var string|null
     */
    protected string|null $resource_route = null;

    /**
     * Link menu item.
     *
     * @var string|null link
     */
    protected string|null $link = null;

    /**
     * Item menu type.
     *
     * @var string|null current.type
     */
    protected string|null $type = null;

    /**
     * Item menu controller.
     *
     * @var string|null controller
     */
    protected string|null $controller = null;

    /**
     * Item menu model class.
     *
     * @var string|null model_class
     */
    protected string|null $model_class = null;

    /**
     * Current application route.
     *
     * @var string|null current_route
     */
    protected string|null $current_route = null;

    /**
     * Additional post for the item menu.
     *
     * @var string|null post
     */
    protected string|null $post = null;

    /**
     * Title menu item for the page header.
     *
     * @var string|null head_title
     */
    protected string|null $head_title = null;

    /**
     * Title for the menu item header.
     *
     * @var string|null main_header
     */
    protected string|null $main_header = null;

    /**
     * Template for navbar.
     *
     * @var string|null nav_bar_view
     */
    protected string|null $nav_bar_view = null;

    /**
     * Vue template for navbar.
     *
     * @var string|null nav_bar_vue
     */
    protected string|null $nav_bar_vue = null;

    /**
     * Left template for the navbar.
     *
     * @var string|null left_nav_bar_view
     */
    protected string|null $left_nav_bar_view = null;

    /**
     * Left Vue template for the navbar.
     *
     * @var string|null left_nav_bar_vue
     */
    protected string|null $left_nav_bar_vue = null;

    /**
     * Action menu item.
     *
     * @var array|string|null action
     */
    protected array|string|null $action = null;

    /**
     * Item menu resource.
     *
     * @var array|null resource
     */
    protected array|null $resource = null;

    /**
     * Roles to whom the item menu is visible.
     *
     * @var array|null roles
     */
    protected array|null $roles = null;

    /**
     * Parameters for generating a post route link.
     *
     * @var array|null route_params
     */
    protected array|null $route_params = null;

    /**
     * List of resource types that can be generated.
     *
     * @var array|null resource_only
     */
    protected array|null $resource_only = null;

    /**
     * An exclusive list of resource types that can be generated.
     *
     * @var array|null resource_except
     */
    protected array|null $resource_except = null;

    /**
     * Badge menu item.
     *
     * @var array|null badge
     */
    protected array|null $badge = null;

    /**
     * Item menu data.
     *
     * @var array|null data
     */
    protected array|null $data = null;

    /**
     * Parameters for the navbar.
     *
     * @var array|null params
     */
    protected array|null $params = null;

    /**
     * Current menu item model.
     *
     * @var mixed|null model
     */
    protected mixed $model = null;

    /**
     * Do not use the item menu in global search.
     *
     * @var bool
     */
    protected bool $dontUseSearch = false;

    /**
     * Whether the menu item is selected.
     *
     * @var bool selected
     */
    protected bool $selected = false;

    /**
     * The admin panel is located on the current menu item.
     *
     * @var bool current
     */
    protected bool $current = false;

    /**
     * Whether to add a target to a blank menu item.
     *
     * @var bool target
     */
    protected bool $target = false;

    /**
     * Whether the item menu is active.
     *
     * @var bool active
     */
    protected bool $active = false;

    /**
     * Whether to add a target to a blank menu item.
     *
     * @var bool target blank
     */
    protected bool $targetBlank = false;

    /**
     * Get the instance menu item as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        $data = [];
        foreach (get_object_vars($this) as $key => $value) {
            if ($value !== null && $value !== '' && $value !== [] && !is_object($value)) {
                if ($key === 'title') {
                    $data[$key] = __($value);
                } else {
                    $data[$key] = $value;
                }
            } else if ($value instanceof Collection) {
                $data[$key] = $value->toArray();
            }
        }
        return $data;
    }

    /**
     * Get item menu ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set item menu ID.
     *
     * @param  int|null  $id
     */
    public function setId(int|null $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the ID of the parent menu item.
     *
     * @return int|null
     */
    public function getParentId(): int|null
    {
        return $this->parent_id;
    }

    /**
     * Set the parent ID of the item menu.
     *
     * @param  int|null  $parent_id
     */
    public function setParentId(int|null $parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    /**
     * Get roles for whom menu item.
     *
     * @return array|null
     */
    public function getRoles(): array|null
    {
        return $this->roles;
    }

    /**
     * Set the roles for whom the item menu.
     *
     * @param  array|null  $roles
     */
    public function setRoles(array|null $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Combine roles for whom menu item.
     *
     * @param  array|null  $roles
     */
    public function mergeRoles(array|null $roles): void
    {
        $this->roles = array_merge($this->roles ?: [], $roles ?: []);
    }

    /**
     * Get the current item menu link.
     *
     * @return string|null
     */
    public function getLink(): string|null
    {
        return $this->link;
    }

    /**
     * Set the current item menu link.
     *
     * @param  string|null  $link
     */
    public function setLink(string|null $link = null): void
    {
        $this->link = $link === null
            ? route($this->route, $this->route_params)
            : $link;
    }

    /**
     * Generate a resource link with the specified type.
     *
     * @param  string  $type
     * @param  mixed  $params
     * @param  bool  $rememberedQuery
     * @return string|null
     */
    protected function generateResourceLink(string $type, mixed $params, bool $rememberedQuery): string|null
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
     * Get the current menu route of an item.
     *
     * @return string|null
     */
    public function getRoute(): string|null
    {
        return $this->route;
    }

    /**
     * Set the current item menu route.
     *
     * @param  string|null  $route
     */
    public function setRoute(string|null $route): void
    {
        $this->route = $route;
    }

    /**
     * Get a resource link for displaying the item menu.
     *
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkShow(mixed $params): string|null
    {
        return $this->generateResourceLink('show', $params, true);
    }

    /**
     * Get a resource link for updating the item menu.
     *
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkUpdate(mixed $params): ?string
    {
        return $this->generateResourceLink('update', $params, false);
    }

    /**
     * Get the resource link for destroying the item menu.
     *
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkDestroy(mixed $params): string|null
    {
        return $this->generateResourceLink('destroy', $params, false);
    }

    /**
     * Get a link to the resource for editing the item menu.
     *
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkEdit(mixed $params): string|null
    {
        return $this->generateResourceLink('edit', $params, true);
    }

    /**
     * Get a link to the item menu index resource.
     *
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkIndex(mixed $params = []): string|null
    {
        return $this->generateResourceLink('index', $params, true);
    }

    /**
     * Get a link to the resource for creating an item menu.
     *
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkCreate(mixed $params = []): string|null
    {
        return $this->generateResourceLink('create', $params, true);
    }

    /**
     * Get a link to the resource for saving the item menu.
     *
     * @param  mixed  $params
     * @return string|null
     */
    public function getLinkStore(mixed $params = []): string|null
    {
        return $this->generateResourceLink('store', $params, false);
    }

    /**
     * Get whether the menu item is selected.
     *
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

    /**
     * Set whether the menu item is selected.
     *
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
     * Does the item menu have a blank target?
     *
     * @return bool
     */
    public function isTarget(): bool
    {
        return $this->target;
    }

    /**
     * Set whether the item menu has a blank target.
     *
     * @param  bool|null  $target
     */
    public function setTarget(?bool $target = null): void
    {
        $target = $target === null
            ? str_starts_with($this->link ?: '', 'http')
            : $target;

        $this->target = $target;
    }

    /**
     * Check if the item is not the current menu item selected in the admin panel.
     *
     * @return bool
     */
    public function isNotCurrent(): bool
    {
        return !$this->isCurrent();
    }

    /**
     * Check if the current menu item is selected in the admin panel.
     *
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * Set if the current menu item is selected in the admin panel.
     *
     * @param  bool  $current
     */
    public function setCurrent(bool $current): void
    {
        $this->current = $current;
    }

    /**
     * Is a menu item resource.
     *
     * @return bool
     */
    public function isResource(): bool
    {
        return !!$this->type;
    }

    /**
     * Check the item menu type is equal to the specified one.
     *
     * @param  string  $type
     * @return bool
     */
    public function isType(string $type): bool
    {
        return $this->type === $type;
    }

    /**
     * If the active menu item.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Set if menu item is active.
     *
     * @param  bool  $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * Get the title of the item menu.
     *
     * @return string|null
     */
    public function getTitle(): string|null
    {
        return $this->title ? __($this->title) : $this->title;
    }

    /**
     * Set the title of the item menu.
     *
     * @param  string|null  $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get the item menu icon.
     *
     * @return string|null
     */
    public function getIcon(): string|null
    {
        return $this->icon;
    }

    /**
     * Set the item menu icon.
     *
     * @param  string|null  $icon
     */
    public function setIcon(string|null $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * Get menu item menu route resource.
     *
     * @return string|null
     */
    public function getResourceRoute(): string|null
    {
        return $this->resource_route;
    }

    /**
     * Set item menu route resource.
     *
     * @param  string|null  $resource_route
     */
    public function setResourceRoute(string|null $resource_route): void
    {
        $this->resource_route = $resource_route;
    }

    /**
     * Get item menu actions.
     *
     * @return array|null
     */
    public function getAction(): array|null
    {
        return $this->action;
    }

    /**
     * Set item menu actions.
     *
     * @param  array|null  $action
     */
    public function setAction(array|null $action): void
    {
        $this->action = $action;
    }

    /**
     * Get the current menu item resource.
     *
     * @return array|null
     */
    public function getResource(): array|null
    {
        return $this->resource;
    }

    /**
     * Set the current item menu resource.
     *
     * @param  array|null  $resource
     */
    public function setResource(array|null $resource): void
    {
        $this->resource = $resource;
    }

    /**
     * Get the current action of the item menu resource.
     *
     * @return string|null
     */
    public function getResourceAction(): string|null
    {
        return isset($this->resource['action'])
            ? ltrim($this->resource['action'], '\\')
            : null;
    }

    /**
     * Get the current menu item model.
     *
     * @return mixed|null
     */
    public function getModel(): mixed
    {
        return $this->model;
    }

    /**
     * Set the current menu item model.
     *
     * @param  mixed|null  $model
     */
    public function setModel(mixed $model): void
    {
        $this->model = $model;
    }

    /**
     * Merge parameters to generate item menu route.
     *
     * @param  array|null  $route_params
     */
    public function mergeRouteParams(array|null $route_params): void
    {
        $this->route_params = array_merge($this->route_params ?: [], $route_params ?: []);
    }

    /**
     * Get parameters for generating an item menu route.
     *
     * @return array|null
     */
    public function getRouteParams(): array|null
    {
        return $this->route_params;
    }

    /**
     * Set parameters for item menu routes.
     *
     * @param  array|null  $route_params
     */
    public function setRouteParams(array|null $route_params): void
    {
        $this->route_params = $route_params;
    }

    /**
     * Get the current item menu controller.
     *
     * @return string|null
     */
    public function getController(): string|null
    {
        return $this->controller;
    }

    /**
     * Set the current item menu controller.
     *
     * @param  string|null  $controller
     */
    public function setController(string|null $controller = null): void
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
     * Insert parent route of item menu.
     *
     * @param  string|null  $routeName
     * @return void
     */
    public function insertParentRouteName(string|null $routeName): void
    {
        if ($routeName) {
            if (str_replace(['{', '?', '}'], '', $this->route ?: '') != $this->route) {
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
     * Get the name of the item menu resource.
     *
     * @return string|null
     */
    public function getResourceName(): string|null
    {
        return $this->resource['name'] ?? null;
    }

    /**
     * Get the extension provider to which the item menu is attached.
     *
     * @return ExtendProvider|null
     */
    public function getExtension(): ExtendProvider|null
    {
        return $this->extension;
    }

    /**
     * Set provider of the extension to which the item menu is attached.
     *
     * @param  ExtendProvider|null  $extension
     */
    public function setExtension(ExtendProvider|null $extension): void
    {
        $this->extension = $extension;
    }

    /**
     * Get the current item menu type.
     *
     * @return string|null
     */
    public function getType(): string|null
    {
        return $this->type;
    }

    /**
     * Set the current item menu type.
     *
     * @param  string|null  $type
     */
    public function setType(string|null $type): void
    {
        $this->type = $type;
    }

    /**
     * Get a list of resource types that you need to use.
     *
     * @return array|null
     */
    public function getResourceOnly(): array|null
    {
        return $this->resource_only;
    }

    /**
     * Set up a list of resource types that you want to use.
     *
     * @param  array|null  $resource_only
     */
    public function setResourceOnly(array|null $resource_only): void
    {
        $this->resource_only = $resource_only;
    }

    /**
     * Get a list of resource types that need to be excluded.
     *
     * @return array|null
     */
    public function getResourceExcept(): array|null
    {
        return $this->resource_except;
    }

    /**
     * Set a list of resource types that need to be excluded.
     *
     * @param  array|null  $resource_except
     */
    public function setResourceExcept(array|null $resource_except): void
    {
        $this->resource_except = $resource_except;
    }

    /**
     * Get the class of the current model.
     *
     * @return string|null
     */
    public function getModelClass(): string|null
    {
        return $this->model_class;
    }

    /**
     * Set the class of the current model.
     *
     * @param  string|null  $model_class
     */
    public function setModelClass(string|null $model_class = null): void
    {
        if ($model_class === null && $controller = $this->controller) {
            if (method_exists($controller, 'getModel')) {
                $model_class = call_user_func([$controller, 'getModel']);
            } elseif (property_exists($controller, 'model')) {
                $model_class = $controller::$model;
            } else {
                $model_class = null;
            }
        }

        $this->model_class = $model_class;
    }

    /**
     * Get the class of the current menu item route.
     *
     * @return string|null
     */
    public function getCurrentRoute(): string|null
    {
        return $this->current_route;
    }

    /**
     * Set the class of the current menu item route.
     *
     * @param  string|null  $current_route
     */
    public function setCurrentRoute(string|null $current_route): void
    {
        $this->current_route = $current_route;
    }

    /**
     * Get a collection of parent menu items.
     *
     * @return Collection|null
     */
    public function getChild(): Collection|null
    {
        return $this->child;
    }

    /**
     * Set a collection of parent menu items.
     *
     * @param  Collection|null  $child
     */
    public function setChild(Collection|null $child): void
    {
        $this->child = $child;
    }

    /**
     * Get the installed badge for the item menu.
     *
     * @return array|null
     */
    public function getBadge(): array|null
    {
        return $this->badge;
    }

    /**
     * Set a badge for the item menu.
     *
     * @param  array|null  $badge
     */
    public function setBadge(array|null $badge): void
    {
        $this->badge = $badge;
    }

    /**
     * Get the label that is responsible for creating and post the route for the item menu.
     *
     * @return string|null
     */
    public function getPost(): string|null
    {
        return $this->post;
    }

    /**
     * Set a label that is responsible for creating a post route for the item menu.
     *
     * @param  string|null  $post
     */
    public function setPost(string|null $post): void
    {
        $this->post = $post;
    }

    /**
     * Get all item menu data.
     *
     * @return array|null
     */
    public function getData(): array|null
    {
        return $this->data;
    }

    /**
     * Set item menu data.
     *
     * @param  array|null  $data
     */
    public function setData(array|null $data): void
    {
        $this->data = $data;
    }

    /**
     * Get title menu item for the page header.
     *
     * @return string|null
     */
    public function getHeadTitle(): string|null
    {
        return $this->head_title;
    }

    /**
     * Set title menu item for the page header.
     *
     * @param  string|null  $head_title
     */
    public function setHeadTitle(string|null $head_title): void
    {
        $this->head_title = $head_title;
    }

    /**
     * Get title for the menu item header.
     *
     * @return string|null
     */
    public function getMainHeader(): string|null
    {
        return $this->main_header;
    }

    /**
     * Set title for the menu item header.
     *
     * @param  string|null  $main_header
     */
    public function setMainHeader(string|null $main_header): void
    {
        $this->main_header = $main_header;
    }

    /**
     * Get template for navbar.
     *
     * @return string|null
     */
    public function getNavBarView(): string|null
    {
        return $this->nav_bar_view;
    }

    /**
     * Set template for navbar.
     *
     * @param  string|null  $nav_bar_view
     */
    public function setNavBarView(string|null $nav_bar_view): void
    {
        $this->nav_bar_view = $nav_bar_view;
    }

    /**
     * Get left template for the navbar.
     *
     * @return string|null
     */
    public function getLeftNavBarView(): string|null
    {
        return $this->left_nav_bar_view;
    }

    /**
     * Set left template for the navbar.
     *
     * @param  string|null  $left_nav_bar_view
     */
    public function setLeftNavBarView(string|null $left_nav_bar_view): void
    {
        $this->left_nav_bar_view = $left_nav_bar_view;
    }

    /**
     * Get parameters for the navbar.
     *
     * @return array|null
     */
    public function getParams(): array|null
    {
        return $this->params;
    }

    /**
     * Set parameters for the navbar.
     *
     * @param  array|null  $params
     */
    public function setParams(array|null $params): void
    {
        $this->params = $params;
    }

    /**
     * Whether a offset exists
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, $offset);
    }

    /**
     * Offset to retrieve
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset};
    }

    /**
     * Offset to set
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    /**
     * Offset to unset
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->{$offset} = null;
    }

    /**
     * @return bool
     */
    public function getDontUseSearch(): bool
    {
        return $this->dontUseSearch;
    }

    /**
     * Find out if it is possible not to use the item menu in the global search.
     *
     * @param  bool  $dontUseSearch
     * @return void
     */
    public function setDontUseSearch(bool $dontUseSearch): void
    {
        $this->dontUseSearch = $dontUseSearch;
    }

    /**
     * Get Vue template for navbar.
     *
     * @return string|null
     */
    public function getNavBarVue(): string|null
    {
        return $this->nav_bar_vue;
    }

    /**
     * Set Vue template for navbar.
     *
     * @param  string|null  $nav_bar_vue
     * @return void
     */
    public function setNavBarVue(?string $nav_bar_vue): void
    {
        $this->nav_bar_vue = $nav_bar_vue;
    }

    /**
     * Get left Vue template for the navbar.
     *
     * @return string|null
     */
    public function getLeftNavBarVue(): string|null
    {
        return $this->left_nav_bar_vue;
    }

    /**
     * Set left Vue template for the navbar.
     *
     * @param  string|null  $left_nav_bar_vue
     * @return void
     */
    public function setLeftNavBarVue(?string $left_nav_bar_vue): void
    {
        $this->left_nav_bar_vue = $left_nav_bar_vue;
    }

    /**
     * Get whether to insert the target blank menu item.
     *
     * @return bool
     */
    public function isTargetBlank(): bool
    {
        return $this->targetBlank;
    }

    /**
     * Set whether to insert the target blank menu item.
     *
     * @param  bool  $targetBlank
     * @return void
     */
    public function setTargetBlank(bool $targetBlank): void
    {
        $this->targetBlank = $targetBlank;
    }
}
