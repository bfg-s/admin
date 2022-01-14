<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\LtePage;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\SearchForm;
use Lar\LteAdmin\Segments\Tagable\TabContent;

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
     * @param  LteUser  $user
     * @return string
     */
    public function show_role(LteUser $user)
    {
        return '<span class="badge badge-success">' . $user->roles->pluck('name')->implode('</span> <span class="badge badge-success">') . '</span>';
    }

    public function canDelete($type)
    {
        return !($type === 'delete' && $this->model()->id == 1);
    }

    /**
     * @param  LtePage  $page
     * @return LtePage
     */
    public function index(LtePage $page)
    {
        return $page
            ->card('lte.admin_list')
            ->withTools([$this, 'canDelete'])
            ->search(function (SearchForm $form) {
                $form->id();
                $form->email('email', 'lte.email_address');
                $form->input('login', 'lte.login_name');
                $form->input('name', 'lte.name');
                $form->at();
            })
            ->table(function (ModelTable $table) {
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
     * @param  LtePage  $page
     * @return LtePage
     */
    public function matrix(LtePage $page)
    {
        return $page
            ->card(['lte.add_admin', 'lte.edit_admin'])
            ->withTools([$this, 'canDelete'])
            ->form(function (Form $form) {
                $form->info_id();
                $form->image('avatar', 'lte.avatar')->nullable();
                $form->tab('lte.common', 'fas fa-cogs', function (TabContent $tab)  {
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
     * @param  LtePage  $page
     * @return LtePage
     */
    public function show(LtePage $page)
    {
        return $page->card()
            ->withTools([$this, 'canDelete'])
            ->info(function (ModelInfoTable $table) {
                $table->row('lte.avatar', 'avatar')->avatar(150);
                $table->row('lte.role', [$this, 'show_role']);
                $table->row('lte.email_address', 'email');
                $table->row('lte.login_name', 'login');
                $table->row('lte.name', 'name');
                $table->at();
            })
            ->card('lte.activity', function (Card $card) {
                $card->warning();
                UserController::activityComponent($card->body(), $this->model()->logs());
            })
            ->card('lte.timeline', function (Card $card) {
                $card->danger();
                UserController::timelineComponent($card->body(), $this->model()->logs());
            });
    }
}
