<?php

namespace Lar\LteAdmin\Controllers;

use Lar\Layout\Abstracts\Component;
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
use Lar\LteAdmin\Segments\Tagable\Tabs;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class AdminsController extends Controller
{
    /**
     * @var string
     */
    static $model = \Lar\LteAdmin\Models\LteUser::class;

    /**
     * @return Sheet
     */
    public function index()
    {
        return Sheet::create('lte.admin_list', function (ModelTable $table, Card $card) {

            $card->search(function (SearchForm $form) {

                $form->id();
                $form->email('email', 'lte.email_address');
                $form->input('login', 'lte.login_name', '=%');
                $form->input('name', 'lte.name', '=%');
                $form->at();
            });

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
        return new Matrix($this->isType('create') ? 'lte.add_admin' : 'lte.edit_admin', function (Form $form, Card $card) {

            $card->defaultTools(function ($type) {
                return $type === 'delete' && $this->model()->id == 1 && admin()->id == $this->model()->id ? false : true;
            });

            $form->image('avatar', 'lte.avatar')->nullable();

            $form->input('login', 'lte.login_name')
                ->required()
                ->unique(LteUser::class, 'login', $this->model()->id);

            $form->input('name', 'lte.name')->required();

            $form->email('email', 'lte.email_address')
                ->required()->unique(LteUser::class, 'email', $this->model()->id);

            $form->multi_select('roles[]', 'lte.role')->icon_user_secret()
                ->options(LteRole::all()->pluck('name','id'));

            $form->br()->h5(__('lte.password'))->hr();

            $form->password('password', 'lte.new_password')
                ->confirm();
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
