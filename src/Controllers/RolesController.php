<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Delegates\Card;
use Lar\LteAdmin\Delegates\Form;
use Lar\LteAdmin\Delegates\ModelInfoTable;
use Lar\LteAdmin\Delegates\ModelTable;
use Lar\LteAdmin\Delegates\SearchForm;
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
     * @param  Card  $card
     * @param  SearchForm  $searchForm
     * @param  ModelTable  $modelTable
     * @return Page
     */
    public function index(Page $page, Card $card, SearchForm $searchForm, ModelTable $modelTable)
    {
        return $page->card(
            $card->title('lte.list_of_roles'),
            $card->search_form(
                $searchForm->id(),
                $searchForm->input('name', 'lte.title'),
                $searchForm->input('slug', 'lte.slug'),
                $searchForm->at(),
            ),
            $card->model_table(
                $modelTable->id(),
                $modelTable->col('lte.title', 'name')->sort(),
                $modelTable->col('lte.slug', 'slug')->sort()->badge('success'),
                $modelTable->at(),
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
        return $page->card(
            $card->title(['lte.add_role', 'lte.edit_role']),
            $card->form(
                $form->ifEdit()->info_id(),
                $form->input('name', 'lte.title')->required()->duplication_how_slug('#input_slug'),
                $form->input('slug', 'lte.slug')->required()->slugable(),
                $form->ifEdit()->info_updated_at(),
                $form->ifEdit()->info_created_at(),
            ),
            $card->footer_form(),
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
        return $page->card(
            $card->model_info_table(
                $modelInfoTable->id(),
                $modelInfoTable->row('lte.title', 'name'),
                $modelInfoTable->row('lte.slug', 'slug')->badge('success'),
                $modelInfoTable->at(),
            )
        );
    }
}
