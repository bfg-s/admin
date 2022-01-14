<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Segments\LtePage;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\SearchForm;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class FunctionsController extends Controller
{
    /**
     * @var string
     */
    static $model = LteFunction::class;

    /**
     * @var array
     */
    static $roles = ['root'];

    /**
     * @param  LtePage  $page
     * @return LtePage
     */
    public function index(LtePage $page)
    {
        return $page
            ->card()
            ->withTools()
            ->search(function (SearchForm $form) {
                $form->id();
                $form->input('slug', 'lte.slug');
                $form->input('class', 'Class', '%=%');
                $form->at();
            })
            ->table(function (ModelTable $table) {
                $table->id();
                $table->column('lte.role', [$this, 'show_roles']);
                $table->column('lte.slug', 'slug')->sort()->input_editable()
                    ->copied()->to_prepend_link('fas fa-glasses', null, '{class}');
                $table->column('lte.description', 'description')->to_lang()->has_lang()->str_limit(50)->textarea_editable()->sort();
                $table->active_switcher();
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
            ->card()
            ->withTools()
            ->form(function (Form $form) {
                $form->info_id();
                $form->input('slug', 'lte.slug')->required()
                    ->slugable();
                $form->checks('roles', 'lte.roles')->required()
                    ->options(LteRole::all()->pluck('name', 'id'));
                $form->textarea('description', 'lte.description');
                $form->switcher('active', 'lte.active')->boolean();
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
                $table->row('lte.role', [$this, 'show_roles']);
                $table->row('lte.slug', 'slug')->copied();
                $table->row('lte.description', 'description')->to_lang()->has_lang()->str_limit(50);
                $table->row('lte.active', 'active')->input_switcher();
                $table->at();
            });
    }

    /**
     * @param  LteFunction  $function
     * @return string
     */
    public function show_roles(LteFunction $function)
    {
        return '<span class="badge badge-success">' . $function->roles->pluck('name')->implode('</span> <span class="badge badge-success">') . '</span>';
    }
}
