<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\LtePage;
use Lar\LteAdmin\Segments\Tagable\Card;
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

    public function explanation(): Explanation
    {
        return Explanation::new(
            $this->card()->defaultTools([$this, 'canDelete'])
        )->index(
            $this->search()->id(),
            $this->search()->email('email', 'lte.email_address'),
            $this->search()->input('login', 'lte.login_name'),
            $this->search()->input('name', 'lte.name'),
            $this->search()->at(),
        )->index(
            $this->table()->id(),
            $this->table()->col('lte.avatar', 'avatar')->avatar(),
            $this->table()->col('lte.role', [$this, 'show_role']),
            $this->table()->col('lte.email_address', 'email')->sort(),
            $this->table()->col('lte.login_name', 'login')->sort(),
            $this->table()->col('lte.name', 'name')->sort(),
            $this->table()->at(),
            $this->table()->controlDelete(function (LteUser $user) { return $user->id !== 1 && admin()->id !== $user->id; }),
            $this->table()->disableChecks(),
        )->edit(
            $this->form()->info_id(),
        )->form(
            $this->form()->image('avatar', 'lte.avatar')->nullable(),
            $this->form()->tab('lte.common', 'fas fa-cogs', function (TabContent $tab)  {
                $tab->input('login', 'lte.login_name')
                    ->required()
                    ->unique(LteUser::class, 'login', $this->model()->id);
                $tab->input('name', 'lte.name')->required();
                $tab->email('email', 'lte.email_address')
                    ->required()->unique(LteUser::class, 'email', $this->model()->id);
                $tab->multi_select('roles[]', 'lte.role')->icon_user_secret()
                    ->options(LteRole::all()->pluck('name','id'));
            }),
            $this->form()->tab('lte.password', 'fas fa-key', function (TabContent $tab)  {
                $tab->password('password', 'lte.new_password')
                    ->confirm()->required_condition($this->isType('create'));
            }),
        )->edit(
            $this->form()->info_at(),
        )->show(
            $this->info()->id(),
            $this->info()->row('lte.avatar', 'avatar')->avatar(150),
            $this->info()->row('lte.role', [$this, 'show_role']),
            $this->info()->row('lte.email_address', 'email'),
            $this->info()->row('lte.login_name', 'login'),
            $this->info()->row('lte.name', 'name'),
            $this->info()->at(),
        );
    }

    /**
     * @param  LtePage  $page
     * @return LtePage
     */
    public function index(LtePage $page)
    {
        return $page
            ->card('lte.admin_list')
            ->search()
            ->table();
    }

    /**
     * @param  LtePage  $page
     * @return LtePage
     */
    public function matrix(LtePage $page)
    {
        return $page
            ->card(['lte.add_admin', 'lte.edit_admin'])
            ->form();
    }

    /**
     * @param  LtePage  $page
     * @return LtePage
     */
    public function show(LtePage $page)
    {
        return $page->card()
            ->info()
            ->card('lte.activity', function (Card $card) {
                $body = $card->warning()->fullBody();
                $body->tab('lte.day_activity', 'fas fa-chart-line', function (TabContent $content) {
                    UserController::activityDayComponent($content, $this->model()->logs());
                });
                $body->tab('lte.year_activity', 'fas fa-chart-line', function (TabContent $content) {
                    UserController::activityYearComponent($content, $this->model()->logs());
                });
            })
            ->card('lte.timeline', function (Card $card) {
                $card->danger();
                UserController::timelineComponent($card->body(), $this->model()->logs());
            });
    }
}
