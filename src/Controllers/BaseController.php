<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Core\ModelSaver;
use Illuminate\Routing\Controller;

/**
 * Trait ControllerMethods
 * @package Lar\LteAdmin\Core\Traits
 */
abstract class BaseController extends Controller
{
    /**
     * Save request to model
     *
     * @param  array|null  $data
     * @return bool|void
     */
    public function requestToModel(array $data = null)
    {
        return $this->model() ? ModelSaver::do($this->model(), $data ?? request()->all()) : false;
    }

    /**
     * Get only exists model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Lar\LteAdmin\Getters\Menu|string|null
     */
    public function existsModel()
    {
        return $this->model() && $this->model()->exists ? $this->model() : null;
    }

    /**
     * Get menu model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Lar\LteAdmin\Getters\Menu|string|null
     */
    public function model()
    {
        return gets()->lte->menu->model;
    }

    /**
     * Get now menu
     *
     * @return array|\Lar\LteAdmin\Getters\Menu|null
     */
    public function now()
    {
        return gets()->lte->menu->now;
    }

    /**
     * Get resource type
     *
     * @return \Lar\LteAdmin\Getters\Menu|string|null
     */
    public function type()
    {
        return gets()->lte->menu->type;
    }

    /**
     * Check type for resource
     *
     * @param  string  $type
     * @return bool
     */
    public function isType(string $type)
    {
        return $this->type() === $type;
    }
}