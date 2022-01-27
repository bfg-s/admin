<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Delegates\Card;
use Lar\LteAdmin\Delegates\Form;
use Lar\LteAdmin\Delegates\ModelInfoTable;
use Lar\LteAdmin\Delegates\ModelTable;
use Lar\LteAdmin\Delegates\SearchForm;
use Lar\LteAdmin\Models\LtePermission;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Page;

class PermissionController extends Controller
{
    /**
     * @var string
     */
    public static $model = LtePermission::class;

    /**
     * @var string[]
     */
    public $method_colors = [
        '*' => 'primary',
        'GET' => 'info',
        'POST' => 'success',
        'PUT' => 'warning',
        'DELETE' => 'danger',
    ];

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  SearchForm  $searchForm
     * @param  ModelTable  $modelTable
     * @return Page
     */
    public function index(Page $page, Card $card, SearchForm $searchForm, ModelTable $modelTable)
    {
        return $page
            ->card(
                $card->search_form(
                    $searchForm->id(),
                    $searchForm->input('path', 'lte.path'),
                    $searchForm->select('lte_role_id', 'lte.role')
                        ->options(LteRole::all()->pluck('name', 'id'))->nullable(),
                    $searchForm->at(),
                ),
                $card->model_table(
                    $modelTable->id(),
                    $modelTable->col('lte.path', 'path')->badge('success'),
                    $modelTable->col('lte.methods', [$this, 'show_methods'])->sort('method'),
                    $modelTable->col('lte.state', [$this, 'show_state'])->sort('state'),
                    $modelTable->col('lte.role', 'role.name')->sort('role_id'),
                    $modelTable->col('lte.description', 'description')->str_limit(50)->to_hide(),
                    $modelTable->active_switcher(),
                    $modelTable->updated_at()->to_hide(),
                    $modelTable->created_at(),
                ),
            );
    }

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  Form  $form
     * @return Page
     */
    public function matrix(Page $page, Card $card, Form $form)
    {
        return $page
            ->card(
                $card->form(
                    $form->ifEdit()->info_id(),
                    $form->input('path', 'lte.path')
                        ->required(),
                    $form->multi_select('method[]', 'lte.methods')
                        ->options(collect($this->method_colors)->mapWithKeys(static function ($i, $k) {
                            return [$k => __("lte.method_$k")];
                        })->toArray())
                        ->required(),
                    $form->radios('state', 'lte.state')
                        ->options(['close' => __('lte.close'), 'open' => __('lte.open')], true)
                        ->required(),
                    $form->radios('lte_role_id', 'lte.role')
                        ->options(LteRole::all()->pluck('name', 'id'), true)
                        ->required(),
                    $form->input('description', 'lte.description'),
                    $form->switcher('active', 'lte.active')->switchSize('mini')
                        ->default(1),
                    $form->ifEdit()->info_updated_at(),
                    $form->ifEdit()->info_created_at(),
                ),
            );
    }

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  ModelInfoTable  $modelInfoTable
     * @return Page
     */
    public function show(Page $page, Card $card, ModelInfoTable $modelInfoTable)
    {
        return $page
            ->card(
                $card->model_info_table(
                    $modelInfoTable->id(),
                    $modelInfoTable->row('lte.path', 'path')->badge('success'),
                    $modelInfoTable->row('lte.methods', [$this, 'show_methods']),
                    $modelInfoTable->row('lte.state', [$this, 'show_state']),
                    $modelInfoTable->row('lte.role', 'role.name'),
                    $modelInfoTable->row('lte.active', 'active')->yes_no(),
                    $modelInfoTable->at(),
                )
            );
    }

    /**
     * @param  LtePermission  $permission
     * @return string
     */
    public function show_methods(LtePermission $permission)
    {
        return collect($permission->method)->map(function ($i) {
            return "<span class=\"badge badge-{$this->method_colors[$i]}\">".__("lte.method_$i").'</span>';
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
