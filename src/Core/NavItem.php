<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Contracts\Support\Arrayable;
use Lar\LteAdmin\Core\Traits\FontAwesome;
use Lar\LteAdmin\Core\Traits\NavCommon;

/**
 * Class NavGroup
 * @package Lar\LteAdmin\Core
 */
class NavItem implements Arrayable
{
    use FontAwesome, NavCommon;

    /**
     * @var array 
     */
    protected $items = [];

    /**
     * NavItem constructor.
     * @param  string|null  $title
     * @param  string  $route
     * @param  string|\Closure|null  $action
     */
    public function __construct(string $title = null, string $route = null, $action = null)
    {
        $this->route($route)
            ->title($title)
            ->action($action);
    }

    /**
     * @param  string|null  $title
     * @return $this
     */
    public function head_title(string $title = null)
    {
        if ($title !== null) {

            $this->items['head_title'] = $title;
        }

        return $this;
    }

    /**
     * @param  string|null  $link
     * @return $this
     */
    public function link(string $link = null)
    {
        if ($link !== null) {

            $this->items['link'] = $link;
        }

        return $this;
    }


    /**
     * Route methods
     */

    /**
     * @param  string|null  $where
     * @return $this
     */
    public function where(string $where = null)
    {
        if ($where !== null) {

            $this->items['where'] = $where;
        }

        return $this;
    }

    /**
     * @param  string|null  $method
     * @return $this
     */
    public function method(string $method = null)
    {
        if ($method !== null) {

            $this->items['method'] = strtolower($method);
        }

        return $this;
    }

    /**
     * @param  string|null  $view
     * @return $this
     */
    public function view(string $view = null)
    {
        if ($view !== null) {

            $this->items['view'] = $view;
        }

        return $this;
    }

    /**
     * @param  string|\Closure|array  $action
     * @return $this
     */
    public function action($action)
    {
        if ($action !== null) {

            $this->items['action'] = $action;
        }

        return $this;
    }

    /**
     * @param  string  $name
     * @param  string  $resource
     * @param  array  $options
     * @return $this
     */
    public function resource(string $name, string $resource = 'Controller', array $options = [])
    {
        $this->items['resource'] = ['name' => $name, 'action' => $resource, 'options' => $options];

        return $this;
    }

    /**
     * @return $this
     */
    public function ignored()
    {
        $this->items['ignored'] = true;

        return $this;
    }

    /**
     * @param  array  $route_params
     * @return $this
     */
    public function params(array $route_params)
    {
        $this->items['route_params'] = $route_params;

        return $this;
    }

    /**
     * @param  array  $route_params
     * @return $this
     */
    public function model($model)
    {
        $this->items['model'] = $model;

        return $this;
    }

    /**
     * @param  string|array  $action
     * @return $this
     */
    public function post($action = 'Controller@update')
    {
        $this->items['post'] = $action;

        return $this;
    }



    /**
     * @param  array  $params
     * @return $this
     */
    public function badge_params(array $params)
    {
        if ($this->items['badge']) {

            $this->items['badge']['params'] = $params;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->items;
    }
}