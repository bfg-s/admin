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
        $save = $data ?? request()->all();

        foreach (static::$crypt_fields as $crypt_field) {
            if (array_key_exists($crypt_field, $save)) {
                if ($save[$crypt_field]) {
                    $save[$crypt_field] = bcrypt($save[$crypt_field]);
                } else {
                    unset($save[$crypt_field]);
                }
            }
        }

        return $this->model() ? ModelSaver::do($this->model(), $save) : false;
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

    /**
     * @param  null  $name
     * @param  null  $default
     * @return \Lar\LteAdmin\Getters\Menu|string|null|mixed
     */
    public function data($name = null, $default = null)
    {
        if (!$name) {

            return gets()->lte->menu->data;
        }

        return gets()->lte->menu->data($name, $default);
    }
}