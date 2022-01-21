<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Page;

class FunctionsController extends Controller
{
    /**
     * @var string
     */
    public static $model = LteFunction::class;

    /**
     * @var array
     */
    public static $roles = ['root'];

    public function index(Page $page)
    {
        return $page
            ->card()
            ->search_form(
                $this->search_form->id(),
                $this->search_form->input('slug', 'lte.slug'),
                $this->search_form->input('class', 'Class', '%=%'),
                $this->search_form->updated_at(),
                $this->search_form->created_at(),
            )
            ->model_table(
                $this->model_table->id(),
                $this->model_table->col('lte.role', [$this, 'show_roles']),
                $this->model_table->col('lte.slug', 'slug')->sort()->input_editable()
                    ->copied()->to_prepend_link('fas fa-glasses', null, '{class}'),
                $this->model_table->col('lte.description', 'description')->to_lang()
                    ->has_lang()->str_limit(50)->textarea_editable()->sort(),
                $this->model_table->active_switcher(),
                $this->model_table->at(),
            );
    }

    public function matrix(Page $page)
    {
        return $page
            ->card()
            ->form(
                $this->form->info_id(),
                $this->form->input('slug', 'lte.slug')->required()
                    ->slugable(),
                $this->form->checks('roles', 'lte.roles')->required()
                    ->options(LteRole::all()->pluck('name', 'id')),
                $this->form->textarea('description', 'lte.description'),
                $this->form->switcher('active', 'lte.active')->boolean(),
                $this->form->info_at(),
            );
    }

    public function show(Page $page)
    {
        return $page
            ->card()
            ->model_info_table(
                $this->model_info_table->id(),
                $this->model_info_table->row('lte.role', [$this, 'show_roles']),
                $this->model_info_table->row('lte.slug', 'slug')->copied(),
                $this->model_info_table->row('lte.description', 'description')->to_lang()->has_lang()->str_limit(50),
                $this->model_info_table->row('lte.active', 'active')->input_switcher(),
                $this->model_info_table->at(),
            );
    }

    /**
     * @param  LteFunction  $function
     * @return string
     */
    public function show_roles(LteFunction $function)
    {
        return '<span class="badge badge-success">'.$function->roles->pluck('name')->implode('</span> <span class="badge badge-success">').'</span>';
    }
}
