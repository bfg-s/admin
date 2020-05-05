<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LtePermission;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class PermissionController extends Controller
{
    /**
     * @var string[]
     */
    public $method_colors = [
        '*' => 'primary',
        'GET' => 'success',
        'HEAD' => 'secondary',
        'POST' => 'danger',
        'PUT' => 'warning',
        'PATCH' => 'info',
        'DELETE' => 'light',
        'OPTIONS' => 'dark'
    ];

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $methods = function (LtePermission $permission) {

            return collect($permission->method)->map(function ($i) {
                return "<span class=\"badge badge-{$this->method_colors[$i]}\">{$i}</span>";
            })->implode(' ');
        };

        $state = function (LtePermission $permission) {

            return "<span class=\"badge badge-".($permission->state === 'open' ? 'success' : 'danger')."\">".($permission->state === 'open' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>')." ".__("lte::admin.{$permission->state}")."</span>";
        };

        return view('lte::permission.list', [
            'methods' => $methods,
            'state' => $state
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('lte::permission.create');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        return view('lte::permission.edit');
    }
}
