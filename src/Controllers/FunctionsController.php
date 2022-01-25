<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Delegates\Card;
use Lar\LteAdmin\Delegates\Form;
use Lar\LteAdmin\Delegates\ModelInfoTable;
use Lar\LteAdmin\Delegates\ModelTable;
use Lar\LteAdmin\Delegates\SearchForm;
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
                    $searchForm->input('slug', 'lte.slug'),
                    $searchForm->input('class', 'Class', '%=%'),
                    $searchForm->updated_at(),
                    $searchForm->created_at(),
                ),
                $card->model_table(
                    $modelTable->id(),
                    $modelTable->col('lte.role', [$this, 'show_roles']),
                    $modelTable->col('lte.slug', 'slug')->sort()->input_editable()
                        ->copied()->to_prepend_link('fas fa-glasses', null, '{class}'),
                    $modelTable->col('lte.description', 'description')->to_lang()
                        ->has_lang()->str_limit(50)->textarea_editable()->sort(),
                    $modelTable->active_switcher(),
                    $modelTable->at(),
                )
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
                    $form->input('slug', 'lte.slug')->required()
                        ->slugable(),
                    $form->checks('roles', 'lte.roles')->required()
                        ->options(LteRole::all()->pluck('name', 'id')),
                    $form->textarea('description', 'lte.description'),
                    $form->switcher('active', 'lte.active')->boolean(),
                    $form->ifEdit()->info_updated_at(),
                    $form->ifEdit()->info_created_at(),
                ),
                $card->footer_form(),
            );
    }

    public function show(Page $page, Card $card, ModelInfoTable $modelInfoTable)
    {
        return $page
            ->card(
                $card->model_info_table(
                    $modelInfoTable->id(),
                    $modelInfoTable->row('lte.role', [$this, 'show_roles']),
                    $modelInfoTable->row('lte.slug', 'slug')->copied(),
                    $modelInfoTable->row('lte.description', 'description')->to_lang()->has_lang()->str_limit(50),
                    $modelInfoTable->row('lte.active', 'active')->input_switcher(),
                    $modelInfoTable->at(),
                )
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
