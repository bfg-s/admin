<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Delegates\Card;
use Admin\Delegates\Form;
use Admin\Delegates\ModelInfoTable;
use Admin\Delegates\ModelTable;
use Admin\Delegates\SearchForm;
use Admin\Models\AdminRole;
use Admin\Page;

/**
 * Admin panel controller for the roles control page.
 */
class RolesController extends Controller
{
    /**
     * The model the admin panel controller works with.
     *
     * @var string
     */
    public static $model = AdminRole::class;

    /**
     * Index method for displaying a list of records and filtering them.
     *
     * @param  Page  $page
     * @param  Card  $card
     * @param  SearchForm  $searchForm
     * @param  ModelTable  $modelTable
     * @return Page
     */
    public function index(
        Page $page,
        Card $card,
        SearchForm $searchForm,
        ModelTable $modelTable
    ): Page {
        return $page->card(
            $card->title('admin.list_of_roles'),
            $card->search_form(
                $searchForm->id(),
                $searchForm->input('name', 'admin.title'),
                $searchForm->input('slug', 'admin.slug'),
                $searchForm->at(),
            ),
            $card->model_table(
                $modelTable->id(),
                $modelTable->col('admin.title', 'name')
                    ->sort()
                    ->to_export()
                    ->input_editable,
                $modelTable->col('admin.slug', 'slug')
                    ->sort()
                    ->badge('success')
                    ->to_export(),
                $modelTable->at(),
            ),
        );
    }

    /**
     * Form method for displaying the form for editing and adding a record.
     *
     * @param  Page  $page
     * @param  Card  $card
     * @param  Form  $form
     * @return Page
     */
    public function matrix(
        Page $page,
        Card $card,
        Form $form
    ): Page {
        return $page->card(
            $card->title(['admin.add_role', 'admin.edit_role']),
            $card->form(
                $form->ifEdit()->info_id(),
                $form->input('name', 'admin.title')
                    ->required()
                    ->duplication_how_slug('#input_slug'),
                $form->input('slug', 'admin.slug')
                    ->required()
                    ->slugable(),
                $form->ifEdit()->info_updated_at(),
                $form->ifEdit()->info_created_at(),
            ),
            $card->footer_form(),
        );
    }

    /**
     * Display method for displaying information about a record.
     *
     * @param  Page  $page
     * @param  Card  $card
     * @param  ModelInfoTable  $modelInfoTable
     * @return Page
     */
    public function show(
        Page $page,
        Card $card,
        ModelInfoTable $modelInfoTable
    ): Page {
        return $page->card(
            $card->model_info_table(
                $modelInfoTable->id(),
                $modelInfoTable->row('admin.title', 'name'),
                $modelInfoTable->row('admin.slug', 'slug')
                    ->badge('success'),
                $modelInfoTable->at(),
            )
        );
    }
}
