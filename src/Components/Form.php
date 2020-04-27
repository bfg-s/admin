<?php

namespace Lar\LteAdmin\Components;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Form
 * @package Lar\LteAdmin\Components
 */
class Form extends \Lar\Layout\Tags\FORM
{
    /**
     * @var Model
     */
    public $model;

    /**
     * Col constructor.
     * @param  null  $model
     * @param  string  $method
     * @param  string|null  $action
     * @param  mixed  ...$params
     */
    public function __construct($model = null, string $method = 'post', string $action = null, ...$params)
    {
        parent::__construct();

        $this->model = $model;

        $this->when($params);

        $this->setMethod($method);

        $menu = gets()->lte->menu->now;

        $model = gets()->lte->menu->model;

        $type = gets()->lte->menu->type;

        if (isset($menu['model.param'])) {

            $this->input(['type' => 'hidden', 'name' => '_after', 'value' => session('_after', 'index')])
                ->setDatas(['stated' => '_after']);
        }

        if (!$action && $type && $model && $menu) {

            if ($model) {

                $rk_name = $model->getRouteKeyName();

                $key = $model->getOriginal($rk_name);

                if ($type === 'edit' && isset($menu['link.update'])) {

                    $action = $menu['link.update']($key);
                    $this->hiddens(['_method' => 'PUT']);
                }
                else if ($type === 'create' && isset($menu['link.store'])) {

                    $action = $menu['link.store'];
                }
            }
        }

        else if (isset($menu['post']) && isset($menu['route']) && \Route::has($menu['route'] . '.post')) {

            $action = route($menu['route'] . '.post', $menu['route_params'] ?? []);
        }

        if (!$action) {

            $action = url()->current();
        }

        $this->setAction($action);

        $this->setEnctype('multipart/form-data');

        $this->setId($this->getUnique());

        $this->attr('data-load', 'valid');

        //$this->getId()
    }
}