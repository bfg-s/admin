<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Page;

class RolesController extends Controller
{
    /**
     * @var string
     */
    public static $model = LteRole::class;

    /**
     * @param  Page  $page
     * @return \Lar\LteAdmin\Components\Contents\CardContent|\Lar\LteAdmin\Components\ModelTableComponent|\Lar\LteAdmin\Components\SearchFormComponent|Page|\Lar\LteAdmin\PageMethods
     */
    public function index(Page $page)
    {
        return $page
            ->card('lte.list_of_roles')
            ->search_form(
                $this->search_form->id(),
                $this->search_form->input('name', 'lte.title'),
                $this->search_form->input('slug', 'lte.slug'),
                $this->search_form->at(),
            )
            ->model_table(
                $this->model_table->id(),
                $this->model_table->col('lte.title', 'name')->sort(),
                $this->model_table->col('lte.slug', 'slug')->sort()->badge('success'),
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
            ->card(['lte.add_role', 'lte.edit_role'])
            ->form(
                $this->form->info_id(),
                $this->form->input('name', 'lte.title')->required()->duplication_how_slug('#input_slug'),
                $this->form->input('slug', 'lte.slug')->required()->slugable(),
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
                $this->model_info_table->row('lte.title', 'name'),
                $this->model_info_table->row('lte.slug', 'slug')->badge('success'),
                $this->model_info_table->at(),
            );
    }
}
