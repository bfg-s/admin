<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Models\LtePermission;

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

    public function explanation(): Explanation
    {
        return Explanation::new(
            $this->card()->defaultTools()
        )->index(
            $this->search()->id(),
            $this->search()->input('path', 'lte.path'),
            $this->search()->select('lte_role_id', 'lte.role')
                ->options(\Lar\LteAdmin\Models\LteRole::all()->pluck('name', 'id'))->nullable(),
            $this->search()->at(),
        )->index(
            $this->table()->id(),
            $this->table()->col('lte.description', 'description')->str_limit(50),
            $this->table()->col('lte.path', 'path')->badge('success'),
            $this->table()->col('lte.methods', [$this, 'show_methods'])->sort('method'),
            $this->table()->col('lte.state', [$this, 'show_state'])->sort('state'),
            $this->table()->col('lte.role', 'role.name')->sort('role_id'),
            $this->table()->active_switcher(),
            $this->table()->at(),
        )->edit(
            $this->form()->info_id(),
        )->form(
            $this->form()->input('path', 'lte.path')
                ->required(),
            $this->form()->multi_select('method[]', 'lte.methods')
                ->options(collect(array_merge(['*'], \Illuminate\Routing\Router::$verbs))->mapWithKeys(function($i) {return [$i => $i];})->toArray())
                ->required(),
            $this->form()->radios('state', 'lte.state')
                ->options(['close' => __('lte.close'), 'open' => __('lte.open')], true)
                ->required(),
            $this->form()->radios('lte_role_id', 'lte.role')
                ->options(\Lar\LteAdmin\Models\LteRole::all()->pluck('name', 'id'), true)
                ->required(),
            $this->form()->input('description', 'lte.description'),
            $this->form()->switcher('active', 'lte.active')->switchSize('mini')
                ->default(1),
        )->edit(
            $this->form()->info_at(),
        )->show(
            $this->info()->id(),
            $this->info()->row('lte.path', 'path')->badge('success'),
            $this->info()->row('lte.methods', [$this, 'show_methods']),
            $this->info()->row('lte.state', [$this, 'show_state']),
            $this->info()->row('lte.role', 'role.name'),
            $this->info()->row('lte.active', 'active')->yes_no(),
            $this->info()->at(),
        );
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
