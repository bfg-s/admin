<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LtePermission;
use Lar\LteAdmin\Segments\Info;
use Lar\LteAdmin\Segments\Matrix;
use Lar\LteAdmin\Segments\Sheet;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\SearchForm;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class PermissionController extends Controller
{
    /**
     * @var string
     */
    static $model = \Lar\LteAdmin\Models\LtePermission::class;

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
     * @return Sheet
     */
    public function index()
    {
        return Sheet::create(function (ModelTable $table, Card $card) {
            $card->search(function (SearchForm $form) {
                $form->id();
                $form->input('path', 'lte.path', '=%');
                $form->select('lte_role_id', 'lte.role')
                    ->options(\Lar\LteAdmin\Models\LteRole::all()->pluck('name', 'id'));
                $form->at();
            });
            $table->id();
            $table->column('lte.path', 'path')->badge('success');
            $table->column('lte.methods', [$this, 'show_methods'])->sort('method');
            $table->column('lte.state', [$this, 'show_state'])->sort('state');
            $table->column('lte.role', 'role.name')->sort('role_id');
            $table->active_switcher();
            $table->at();
        });
    }

    /**
     * @return Matrix
     */
    public function matrix()
    {
        return Matrix::create(function (Form $form) {
            $form->input('path', 'lte.path')
                ->required();

            $form->multi_select('method[]', 'lte.methods')
                ->options(collect(array_merge(['*'], \Illuminate\Routing\Router::$verbs))->mapWithKeys(function($i) {return [$i => $i];})->toArray())
                ->required();

            $form->radios('state', 'lte.state')
                ->options(['close' => __('lte.close'), 'open' => __('lte.open')], true)
                ->required();

            $form->radios('lte_role_id', 'lte.role')
                ->options(\Lar\LteAdmin\Models\LteRole::all()->pluck('name', 'id'), true)
                ->required();

            $form->switcher('active', 'lte.active')->switchSize('mini')
                ->default(1);
        });
    }

    /**
     * @return Info
     */
    public function show()
    {
        return Info::create(function (ModelInfoTable $table) {
            $table->id();
            $table->row('lte.path', 'path')->badge('success');
            $table->row('lte.methods', [$this, 'show_methods']);
            $table->row('lte.state', [$this, 'show_state']);
            $table->row('lte.role', 'role.name');
            $table->row('lte.active', 'active')->yes_no();
            $table->at();
        });
    }

    /**
     * @param  LtePermission  $permission
     * @return string
     */
    public function show_methods(LtePermission $permission)
    {
        return collect($permission->method)->map(function ($i) {
            return "<span class=\"badge badge-{$this->method_colors[$i]}\">{$i}</span>";
        })->implode(' ');
    }

    /**
     * @param  LtePermission  $permission
     * @return string
     */
    public function show_state(LtePermission $permission)
    {
        return "<span class=\"badge badge-".($permission->state === 'open' ? 'success' : 'danger')."\">".($permission->state === 'open' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>')." ".__("lte.{$permission->state}")."</span>";
    }
}
