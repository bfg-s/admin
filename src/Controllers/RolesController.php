<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Segments\LtePage;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\SearchForm;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class RolesController extends Controller
{
    /**
     * @var string
     */
    static $model = LteRole::class;

    /**
     * @param  LtePage  $page
     * @return LtePage
     */
    public function index(LtePage $page)
    {
        return $page
            ->card('lte.list_of_roles')
            ->withTools()
            ->search(function (SearchForm $form) {
                $form->id();
                $form->input('name', 'lte.title');
                $form->input('slug', 'lte.slug');
                $form->at();
            })
            ->table(function (ModelTable $table) {
                $table->id();
                $table->column('lte.title', 'name')->sort();
                $table->column('lte.slug', 'slug')->sort()->badge('success');
                $table->at();
            });
    }

    /**
     * @param  LtePage  $page
     * @return LtePage
     */
    public function matrix(LtePage $page)
    {
        return $page
            ->card(['lte.add_role', 'lte.edit_role'])
            ->withTools()
            ->form(function (Form $form) {
                $form->info_id();
                $form->input('name', 'lte.title')->required()->duplication_how_slug('#input_slug');
                $form->input('slug', 'lte.slug')->required()->slugable();
                $form->info_at();
            });
    }

    /**
     * @param  LtePage  $page
     * @return LtePage
     */
    public function show(LtePage $page)
    {
        return $page
            ->card()
            ->withTools()
            ->info(function (ModelInfoTable $table) {
                $table->id();
                $table->row('lte.title', 'name');
                $table->row('lte.slug', 'slug')->badge('success');
                $table->at();
            });
    }
}
