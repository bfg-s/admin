<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Segments\Info;
use Lar\LteAdmin\Segments\Matrix;
use Lar\LteAdmin\Segments\Sheet;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\SearchForm;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;

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
     * @return Sheet
     */
    public function index()
    {
        return Sheet::create('lte.list_of_roles', function (ModelTable $table, Card $card) {

            $card->search(function (SearchForm $form) {
                $form->id();
                $form->input('name', 'lte.title', '=%');
                $form->input('slug', 'lte.slug', '=%');
                $form->at();
            });

            $table->id();
            $table->column('lte.title', 'name')->sort();
            $table->column('lte.slug', 'slug')->sort()->badge('success');
            $table->at();
        });
    }

    /**
     * @return Matrix
     */
    public function matrix()
    {
        return Matrix::create(['lte.add_role', 'lte.edit_role'], function (Form $form) {

            $form->input('name', 'lte.title')->required()->duplication_how_slug('#input_slug');
            $form->input('slug', 'lte.slug')->required()->slugable();
        });
    }

    /**
     * @return Info
     */
    public function show()
    {
        return Info::create(function (ModelInfoTable $table) {

            $table->id();
            $table->row('lte.title', 'name');
            $table->row('lte.slug', 'slug')->badge('success');
            $table->at();
        });
    }
}
