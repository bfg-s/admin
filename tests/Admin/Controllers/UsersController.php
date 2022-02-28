<?php

namespace LteAdmin\Tests\Admin\Controllers;

use LteAdmin\Page;
use LteAdmin\Tests\Admin\Delegates\Card;
use LteAdmin\Tests\Admin\Delegates\Form;
use LteAdmin\Tests\Admin\Delegates\ModelInfoTable;
use LteAdmin\Tests\Admin\Delegates\ModelTable;
use LteAdmin\Tests\Admin\Delegates\SearchForm;
use LteAdmin\Tests\Models\User;

/**
 * UsersController Class.
 * @package LteAdmin\Tests\Admin\Controllers
 */
class UsersController extends Controller
{
    /**
     * Static variable Model.
     * @var string
     */
    public static $model = User::class;

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  SearchForm  $searchForm
     * @param  ModelTable  $modelTable
     * @return Page
     */
    public function index(Page $page, Card $card, SearchForm $searchForm, ModelTable $modelTable): Page
    {
        return $page->card(
            $card->search_form(
                $searchForm->id(),
                $searchForm->input('username', 'Username'),
                $searchForm->input('email', 'Email'),
                $searchForm->at(),
            ),
            $card->model_table(
                $modelTable->id(),
                $modelTable->col('Username', 'username')->sort(),
                $modelTable->col('Email', 'email')->sort(),
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
    public function matrix(Page $page, Card $card, Form $form): Page
    {
        return $page->card(
            $card->form(
                $form->ifEdit()->info_id(),
                $form->input('username', 'Username'),
                $form->input('email', 'Email'),
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
    public function show(Page $page, Card $card, ModelInfoTable $modelInfoTable): Page
    {
        return $page->card(
            $card->model_info_table(
                $modelInfoTable->id(),
                $modelInfoTable->row('Username', 'username'),
                $modelInfoTable->row('Email', 'email'),
                $modelInfoTable->at(),
            )
        );
    }
}
