<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Http\Request;
use Lar\LteAdmin\Components\Contents\CardContent;
use Lar\LteAdmin\Components\TabContentComponent;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Page;

class AdministratorsController extends Controller
{
    /**
     * @var string
     */
    public static $model = LteUser::class;

    /**
     * @param  LteUser  $user
     * @return string
     */
    public function show_role(LteUser $user)
    {
        return '<span class="badge badge-success">'.$user->roles->pluck('name')->implode('</span> <span class="badge badge-success">').'</span>';
    }

    public function defaultTools($type)
    {
        return ! ($type === 'delete' && $this->model()->id == 1);
    }

    /**
     * @param  Page  $page
     * @return Page
     */
    public function index(Page $page)
    {
        return $page
            ->card('lte.admin_list')
            ->search_form(
                $this->search_form->id(),
                $this->search_form->email('email', 'lte.email_address'),
                $this->search_form->input('login', 'lte.login_name'),
                $this->search_form->input('name', 'lte.name'),
                $this->search_form->at(),
            )
            ->model_table(
                $this->model_table->id(),
                $this->model_table->col('lte.avatar', 'avatar')->avatar(),
                $this->model_table->col('lte.role', [$this, 'show_role']),
                $this->model_table->col('lte.email_address', 'email')->sort(),
                $this->model_table->col('lte.login_name', 'login')->sort(),
                $this->model_table->col('lte.name', 'name')->sort(),
                $this->model_table->at(),
                $this->model_table->controlDelete(static function (LteUser $user) {
                    return $user->id !== 1 && admin()->id !== $user->id;
                }),
                $this->model_table->disableChecks(),
            );
    }

    /**
     * @param  Page  $page
     * @return CardContent|\Lar\LteAdmin\Components\FormComponent|Page|\Lar\LteAdmin\PageMethods
     */
    public function matrix(Page $page)
    {
        return $page
            ->card(['lte.add_admin', 'lte.edit_admin'])
            ->form(
                $this->form->info_id(),
                $this->form->image('avatar', 'lte.avatar')->nullable(),
                $this->form->tab('lte.common', 'fas fa-cogs', function (TabContentComponent $tab) {
                    $tab->input('login', 'lte.login_name')
                        ->required()
                        ->unique(LteUser::class, 'login', $this->model()->id);
                    $tab->input('name', 'lte.name')->required();
                    $tab->email('email', 'lte.email_address')
                        ->required()->unique(LteUser::class, 'email', $this->model()->id);
                    $tab->multi_select('roles[]', 'lte.role')->icon_user_secret()
                        ->options(LteRole::all()->pluck('name', 'id'));
                }),
                $this->form->tab('lte.password', 'fas fa-key', function (TabContentComponent $tab) {
                    $tab->password('password', 'lte.new_password')
                        ->confirm()->required_condition($this->isType('create'));
                }),
                $this->form->info_at(),
            );
    }

    /**
     * @param  Page  $page
     * @param  Request  $request
     * @return CardContent|\Lar\LteAdmin\Components\ModelInfoTableComponent|Page|\Lar\LteAdmin\PageMethods
     */
    public function show(Page $page, Request $request)
    {
        return $page->card()
            ->model_info_table(
                $this->model_info_table->id(),
                $this->model_info_table->row('lte.avatar', 'avatar')->avatar(150),
                $this->model_info_table->row('lte.role', [$this, 'show_role']),
                $this->model_info_table->row('lte.email_address', 'email'),
                $this->model_info_table->row('lte.login_name', 'login'),
                $this->model_info_table->row('lte.name', 'name'),
                $this->model_info_table->at(),
            )
            ->card(
                $this->card->title('lte.activity')->warningType(),
                $this->card->tab('lte.day_activity', 'fas fa-chart-line', function (TabContentComponent $content) {
                    UserController::activityDayComponent($content, $this->model()->logs());
                }, ! $request->has('ltelog_page')),
                $this->card->tab('lte.year_activity', 'fas fa-chart-line', function (TabContentComponent $content) {
                    UserController::activityYearComponent($content, $this->model()->logs());
                }, false),
                $this->card->tab('lte.timeline', 'fas fa-clock', function (TabContentComponent $content) {
                    UserController::timelineComponent($content, $this->model()->logs());
                }, $request->has('ltelog_page')),
            );
    }
}
