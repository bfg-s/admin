<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LtePermission;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Page;

class PermissionController extends Controller
{
    /**
     * @var string
     */
    public static $model = \Lar\LteAdmin\Models\LtePermission::class;

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
        'OPTIONS' => 'dark',
    ];

    /**
     * @param  Page  $page
     * @return \Lar\LteAdmin\Components\Contents\CardContent|\Lar\LteAdmin\Components\ModelTableComponent|\Lar\LteAdmin\Components\SearchFormComponent|Page|\Lar\LteAdmin\PageMethods
     */
    public function index(Page $page)
    {
        return $page
            ->card()
            ->search_form(
                $this->search_form->id(),
                $this->search_form->input('path', 'lte.path'),
                $this->search_form->select('lte_role_id', 'lte.role')
                    ->options(LteRole::all()->pluck('name', 'id'))->nullable(),
                $this->search_form->at(),
            )
            ->model_table(
                $this->model_table->id(),
                $this->model_table->col('lte.description', 'description')->str_limit(50),
                $this->model_table->col('lte.path', 'path')->badge('success'),
                $this->model_table->col('lte.methods', [$this, 'show_methods'])->sort('method'),
                $this->model_table->col('lte.state', [$this, 'show_state'])->sort('state'),
                $this->model_table->col('lte.role', 'role.name')->sort('role_id'),
                $this->model_table->active_switcher(),
                $this->model_table->at(),
            );
    }

    /**
     * @param  Page  $page
     * @return \Lar\LteAdmin\Components\Contents\CardContent|\Lar\LteAdmin\Components\FormComponent|Page|\Lar\LteAdmin\PageMethods
     */
    public function matrix(Page $page)
    {
        return $page
            ->card()
            ->form(
                $this->form->info_id(),
                $this->form->input('path', 'lte.path')
                    ->required(),
                $this->form->multi_select('method[]', 'lte.methods')
                    ->options(collect(array_merge(['*'], \Illuminate\Routing\Router::$verbs))->mapWithKeys(static function ($i) {
                        return [$i => $i];
                    })->toArray())
                    ->required(),
                $this->form->radios('state', 'lte.state')
                    ->options(['close' => __('lte.close'), 'open' => __('lte.open')], true)
                    ->required(),
                $this->form->radios('lte_role_id', 'lte.role')
                    ->options(LteRole::all()->pluck('name', 'id'), true)
                    ->required(),
                $this->form->input('description', 'lte.description'),
                $this->form->switcher('active', 'lte.active')->switchSize('mini')
                    ->default(1),
                $this->form->info_at(),
            );
    }

    /**
     * Display the specified resource.
     * @param  Page  $page
     * @return \Lar\LteAdmin\Components\Contents\CardContent|\Lar\LteAdmin\Components\ModelInfoTableComponent|Page|\Lar\LteAdmin\PageMethods
     */
    public function show(Page $page)
    {
        return $page
            ->card()
            ->model_info_table(
                $this->model_info_table->id(),
                $this->model_info_table->row('lte.path', 'path')->badge('success'),
                $this->model_info_table->row('lte.methods', [$this, 'show_methods']),
                $this->model_info_table->row('lte.state', [$this, 'show_state']),
                $this->model_info_table->row('lte.role', 'role.name'),
                $this->model_info_table->row('lte.active', 'active')->yes_no(),
                $this->model_info_table->at(),
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
        return '<span class="badge badge-'.($permission->state === 'open' ? 'success' : 'danger').'">'.($permission->state === 'open' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>').' '.__("lte.{$permission->state}").'</span>';
    }
}
