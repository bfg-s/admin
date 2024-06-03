<?php

declare(strict_types=1);

namespace Admin\Traits;

use Admin\AdminEngine;
use Admin\ExtendProvider;
use Admin\NavigateEngine;
use Closure;
use Illuminate\Support\Str;

/**
 * A trait with general rules and methods for navigation items and groups.
 */
trait NavCommonTrait
{
    /**
     * Add a callback at the time of route generation.
     *
     * @param  callable  $call
     * @return $this
     */
    public function router(callable $call): static
    {
        $this->items['router'][] = $call;

        return $this;
    }

    /**
     * Set the provider of the current extension.
     *
     * @param  ExtendProvider|null  $provider
     * @return $this
     */
    public function extension(ExtendProvider $provider = null): static
    {
        if ($provider) {
            $this->items['extension'] = $provider;
        }

        return $this;
    }

    /**
     * Add roles to whom a menu item or group is available.
     *
     * @param  array|string  $roles
     * @return $this
     */
    public function role(array|string $roles): static
    {
        $this->items['roles'] = is_array($roles) ? $roles : [$roles];

        return $this;
    }

    /**
     * Add date data to a menu item or group.
     *
     * @param  array  $data
     * @return $this
     */
    public function data(array $data): static
    {
        if (!isset($this->items['data'])) {
            $this->items['data'] = $data;
        } else {
            $this->items['data'] = array_merge($this->items['data'], $data);
        }

        return $this;
    }

    /**
     * Add an additional uri to a menu item or group.
     *
     * @param  string  $uri
     * @return $this
     */
    public function uri(string $uri): static
    {
        $this->items['uri'] = $uri;

        return $this;
    }

    /**
     * Add an icon to a menu item or group.
     *
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon): static
    {
        $this->items['icon'] = $icon;

        return $this;
    }

    /**
     * Add a title to a menu item or group.
     *
     * @param  string|null  $title
     * @return $this
     */
    public function title(string $title = null): static
    {
        if ($title !== null) {
            $this->items['title'] = $title;
        }

        return $this;
    }

    /**
     * Disable a menu item or group.
     *
     * @param  bool  $state
     * @return $this
     */
    public function off(bool $state = false): static
    {
        $this->items['active'] = $state;

        return $this;
    }

    /**
     * Set the route of a menu item or group.
     *
     * @param  string|null  $route
     * @return $this
     */
    public function route(string $route = null): static
    {
        if ($route !== null) {
            $this->items['route'] = $route;
        }

        return $this;
    }

    /**
     * Add middleware for a menu item or group.
     *
     * @param $middleware
     * @return $this
     */
    public function middleware($middleware): static
    {
        $this->items['middleware'] = $middleware;

        return $this;
    }

    /**
     * Set a menu item or group badge.
     *
     * @param  null  $data
     * @param  string  $type
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge($data = null, string $type = 'info', array $instructions = null): static
    {
        if (is_array($type)) {
            $instructions = $type;
            $type = 'info';
        }

        if ($data !== null) {
            $this->items['badge'] = [
                'id' => Str::slug(($this->items['route'] ?? ($this->items['name'] ?? false)), '_'),
                'text' => $data,
                'type' => $type,
                'instructions' => $instructions,
            ];
        }

        return $this;
    }

    /**
     * Add badge options to a menu item or group.
     *
     * @param  array  $params
     * @return $this
     */
    public function badge_params(array $params): static
    {
        if ($this->items['badge']) {
            $this->items['badge']['params'] = $params;
        }

        return $this;
    }

    /**
     * Add a badge title to a menu item or group.
     *
     * @param  string  $title
     * @return $this
     */
    public function badge_title(string $title): static
    {
        if ($this->items['badge']) {
            $this->items['badge']['title'] = $title;
        }

        return $this;
    }

    /**
     * Set a danger badge for a menu item or group.
     *
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_danger($data = null, array $instructions = null): static
    {
        return $this->badge($data, 'danger', $instructions);
    }

    /**
     * Set the dark badge for a menu item or group.
     *
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_dark($data = null, array $instructions = null): static
    {
        return $this->badge($data, 'dark', $instructions);
    }

    /**
     * Set the light badge for a menu item or group.
     *
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_light($data = null, array $instructions = null): static
    {
        return $this->badge($data, 'light', $instructions);
    }

    /**
     * Set a pill badge for a menu item or group.
     *
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_pill($data = null, array $instructions = null): static
    {
        return $this->badge($data, 'pill', $instructions);
    }

    /**
     * Set the primary badge of a menu item or group.
     *
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_primary($data = null, array $instructions = null): static
    {
        return $this->badge($data, 'primary', $instructions);
    }

    /**
     * Set the secondary badge of a menu item or group.
     *
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_secondary($data = null, array $instructions = null): static
    {
        return $this->badge($data, 'secondary', $instructions);
    }

    /**
     * Set the success badge for a menu item or group.
     *
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_success($data = null, array $instructions = null): static
    {
        return $this->badge($data, 'success', $instructions);
    }

    /**
     * Set a warning badge for a menu item or group.
     *
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_warning($data = null, array $instructions = null): static
    {
        return $this->badge($data, 'warning', $instructions);
    }

    /**
     * The method that is executed at the end of navigation generation, in this case adds menu items for extensions.
     *
     * @param $name
     */
    protected function includeAfterGroup($name): void
    {
        if (is_string($name) && isset(AdminEngine::$nav_extensions[$name]) && is_array(AdminEngine::$nav_extensions[$name])) {
            foreach (AdminEngine::$nav_extensions[$name] as $item) {
                NavigateEngine::$extension = $item;

                $item->navigator($this);

                NavigateEngine::$extension = null;
            }

            unset(AdminEngine::$nav_extensions[$name]);
        }
    }
}
