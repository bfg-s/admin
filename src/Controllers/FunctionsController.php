<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Segments\Info;
use Lar\LteAdmin\Segments\Matrix;
use Lar\LteAdmin\Segments\Sheet;
use Lar\LteAdmin\Segments\Tagable\Card;
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
     * @return Sheet
     */
    public function index()
    {
        return Sheet::create(function (ModelTable $table, Card $card) {

            $card->search(function (SearchForm $form) {

                $form->id();
                $form->input('slug', 'lte.slug', '=%');
                $form->at();
            });

            $table->id();
            $table->column('lte.role', [$this, 'show_roles']);
            $table->column('lte.slug', 'slug')->sort()->input_editable()->copied();
            $table->column('lte.description', 'description')->to_lang()->has_lang()->str_limit(50)->textarea_editable()->sort();
            $table->active_switcher();
            $table->at();
        });
    }

    /**
     * @return Matrix
     */
    public function matrix()
    {
        return Matrix::create(function (Form $form) {
            $form->info_id();
            $form->input('slug', 'lte.slug')->required()
                ->unique(LteFunction::class, 'slug', $this->model()->id)->slugable();
            $form->checks('roles', 'lte.roles')->required()
                ->options(LteRole::all()->pluck('name', 'id'));
            $form->textarea('description', 'lte.description');
            $form->switcher('active', 'lte.active')->boolean();
            $form->info_at();
        });
    }

    /**
     * @return Info
     */
    public function show()
    {
        return Info::create(function (ModelInfoTable $table) {
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
