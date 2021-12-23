<?php

namespace Lar\LteAdmin\Core\Traits;


use Lar\LteAdmin\ExtendProvider;
use Lar\LteAdmin\LteAdmin;
use Lar\LteAdmin\Navigate;

/**
 * Trait NavCommon
 * @package Lar\LteAdmin\Core\Traits
 */
trait NavCommon
{
    /**
     * @param  \Closure|array  $call
     * @return $this
     */
    public function router($call)
    {
        $this->items['router'][] = $call;

        return $this;
    }

    /**
     * @param  ExtendProvider|null  $provider
     * @return $this
     */
    public function extension(ExtendProvider $provider = null)
    {
        if ($provider) {

            $this->items['extension'] = $provider;
        }

        return $this;
    }

    /**
     * @param string|array $roles
     * @return $this
     */
    public function role($roles)
    {
        $this->items['roles'] = is_array($roles) ? $roles : [$roles];

        return $this;
    }

    /**
     * @param  string  $func
     * @return $this
     */
    public function func(string $func)
    {
        $this->items['func'] = $func;

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function data(array $data)
    {
        if (!isset($this->items['data'])) {
            $this->items['data'] = $data;
        } else {
            $this->items['data'] = array_merge($this->items['data'], $data);
        }

        return $this;
    }

    /**
     * @param  string  $uri
     * @return $this
     */
    public function uri(string $uri)
    {
        $this->items['uri'] = $uri;

        return $this;
    }

    /**
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon)
    {
        $this->items['icon'] = $icon;

        return $this;
    }

    /**
     * @param  string|null  $title
     * @return $this
     */
    public function title(string $title = null)
    {
        if ($title !== null) {

            $this->items['title'] = $title;
        }

        return $this;
    }

    /**
     * @param  bool  $state
     * @return $this
     */
    public function selected(bool $state = true)
    {
        $this->items['selected'] = $state;

        return $this;
    }

    /**
     * @param  bool  $state
     * @return $this
     */
    public function off(bool $state = false)
    {
        $this->items['active'] = $state;

        return $this;
    }

    /**
     * @param  string|null  $route
     * @return $this
     */
    public function route(string $route = null)
    {
        if ($route !== null) {

            $this->items['route'] = $route;
        }

        return $this;
    }

    /**
     * @param $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        $this->items['middleware'] = $middleware;

        return $this;
    }

    /**
     * @param  null  $data
     * @param  string  $type
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge($data = null, $type = 'info', array $instructions =  null)
    {
        if (is_array($type)) {
            $instructions = $type;
            $type = 'info';
        }

        if ($data !== null) {

            $this->items['badge'] = [
                'id' => \Str::slug(($this->items['route'] ?? ($this->items['name'] ?? false)), '_'),
                'text' => $data,
                'type' => $type,
                'instructions' => $instructions
            ];
        }

        return $this;
    }

    /**
     * @param  string  $title
     * @return $this
     */
    public function badge_title(string $title)
    {
        if ($this->items['badge']) {

            $this->items['badge']['title'] = $title;
        }

        return $this;
    }

    /**
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_danger($data = null, array $instructions =  null)
    {
        return $this->badge($data, 'danger', $instructions);
    }

    /**
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_dark($data = null, array $instructions =  null)
    {
        return $this->badge($data, 'dark', $instructions);
    }

    /**
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_light($data = null, array $instructions =  null)
    {
        return $this->badge($data, 'light', $instructions);
    }

    /**
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_pill($data = null, array $instructions =  null)
    {
        return $this->badge($data, 'pill', $instructions);
    }

    /**
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_primary($data = null, array $instructions =  null)
    {
        return $this->badge($data, 'primary', $instructions);
    }

    /**
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_secondary($data = null, array $instructions =  null)
    {
        return $this->badge($data, 'secondary', $instructions);
    }

    /**
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_success($data = null, array $instructions =  null)
    {
        return $this->badge($data, 'success', $instructions);
    }

    /**
     * @param  null  $data
     * @param  array|null  $instructions
     * @return $this
     */
    public function badge_warning($data = null, array $instructions =  null)
    {
        return $this->badge($data, 'warning', $instructions);
    }

    /**
     * @return void
     */
    public function injectExtensionHere()
    {
        $extensions = LteAdmin::$nav_extensions;

        foreach ($extensions as $key => $extension) {

            if ($key === 'application') {
                continue;
            }

            if (is_array($extension)) {

                foreach ($extension as $item) {

                    Navigate::$extension = $item;

                    $item->navigator($this);

                    Navigate::$extension = null;
                }

            }
            else {

                Navigate::$extension = $extension;

                $extension->navigator($this);

                Navigate::$extension = null;
            }

            unset(LteAdmin::$nav_extensions[$key]);
        }
    }

    /**
     * @param $name
     * @param $to
     */
    protected function includeAfterGroup($name)
    {
        if (is_string($name) && isset(LteAdmin::$nav_extensions[$name]) && is_array(LteAdmin::$nav_extensions[$name])) {

            foreach (LteAdmin::$nav_extensions[$name] as $item) {

                Navigate::$extension = $item;

                $item->navigator($this);

                Navigate::$extension = null;
            }


            unset(LteAdmin::$nav_extensions[$name]);
        }
    }
}
