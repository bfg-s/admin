<?php

namespace Lar\LteAdmin\Controllers;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\Container;
use Lar\LteAdmin\Segments\Info;
use Lar\LteAdmin\Segments\Matrix;
use Lar\LteAdmin\Segments\Sheet;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\SearchForm;
use Lar\LteAdmin\Segments\Tagable\TabContent;
use Lar\LteAdmin\Segments\Tagable\Tabs;

/**
 * Class AdministratorsController
 * @package Lar\LteAdmin\Controllers
 */
class AdministratorsController extends Controller
{
    /**
     * @var string
     */
    static $model = LteUser::class;

    /**
     * @return Sheet
     */
    public function index()
    {
        return Sheet::create('lte.admin_list', function (ModelTable $table) {

            $table->search->email('email', 'lte.email_address');
            $table->search->input('login', 'lte.login_name', '=%');
            $table->search->input('name', 'lte.name', '=%');
            $table->search->at();

            $table->id();
            $table->column('lte.avatar', 'avatar')->avatar();
            $table->column('lte.role', [$this, 'show_role']);
            $table->column('lte.email_address', 'email')->sort();
            $table->column('lte.login_name', 'login')->sort();
            $table->column('lte.name', 'name')->sort();
            $table->at();
            $table->controlDelete(function (LteUser $user) { return $user->id !== 1 && admin()->id !== $user->id; });
            $table->disableChecks();
        });
    }

    /**
     * @return Matrix
     */
    public function matrix()
    {
        return new Matrix(['lte.add_admin', 'lte.edit_admin'], function (Form $form, Card $card) {

            $card->defaultTools(function ($type) {
                return $type === 'delete' && $this->model()->id == 1 && admin()->id == $this->model()->id ? false : true;
            });

            $form->info_id();

            $form->tab('lte.common', 'fas fa-cogs', function (TabContent $tab)  {

                $tab->image('avatar', 'lte.avatar')->nullable();

                $tab->input('login', 'lte.login_name')
                    ->required()
                    ->unique(LteUser::class, 'login', $this->model()->id);

                $tab->input('name', 'lte.name')->required();

                $tab->email('email', 'lte.email_address')
                    ->required()->unique(LteUser::class, 'email', $this->model()->id);

                $tab->multi_select('roles[]', 'lte.role')->icon_user_secret()
                    ->options(LteRole::all()->pluck('name','id'));
            });

            $form->tab('lte.password', 'fas fa-key', function (TabContent $tab)  {

                $tab->password('password', 'lte.new_password')
                    ->confirm()->required_condition($this->isType('create'));
            });

            $form->info_at();
        });
    }

    /**
     * @return Container
     */
    public function show()
    {
        return Info::create(function (ModelInfoTable $table, Card $card) {
            $card->defaultTools(function ($type) {
                return $type === 'delete' && $this->model()->id == 1 ? false : true;
            });

            $table->row('lte.avatar', 'avatar')->avatar(150);
            $table->row('lte.role', [$this, 'show_role']);
            $table->row('lte.email_address', 'email');
            $table->row('lte.login_name', 'login');
            $table->row('lte.name', 'name');
            $table->at();
        });
    }

    /**
     * @param  LteUser  $user
     * @return string
     */
    public function show_role(LteUser $user)
    {
        return '<span class="badge badge-success">' . $user->roles->pluck('name')->implode('</span> <span class="badge badge-success">') . '</span>';
    }
}
