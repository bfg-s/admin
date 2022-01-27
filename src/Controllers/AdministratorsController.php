<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Http\Request;
use Lar\LteAdmin\Delegates\Card;
use Lar\LteAdmin\Delegates\ChartJs;
use Lar\LteAdmin\Delegates\Form;
use Lar\LteAdmin\Delegates\ModelInfoTable;
use Lar\LteAdmin\Delegates\ModelTable;
use Lar\LteAdmin\Delegates\SearchForm;
use Lar\LteAdmin\Delegates\Tab;
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
        return !($type === 'delete' && $this->model()->id == 1);
    }

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
            $card->title('lte.admin_list'),
            $card->search_form(
                $searchForm->id(),
                $searchForm->email('email', 'lte.email_address'),
                $searchForm->input('login', 'lte.login_name'),
                $searchForm->input('name', 'lte.name'),
                $searchForm->at(),
            ),
            $card->model_table(
                $modelTable->id(),
                $modelTable->col('lte.avatar', 'avatar')->avatar(),
                $modelTable->col('lte.role', [$this, 'show_role']),
                $modelTable->col('lte.email_address', 'email')->sort(),
                $modelTable->col('lte.login_name', 'login')->sort(),
                $modelTable->col('lte.name', 'name')->sort(),
                $modelTable->at(),
                $modelTable->controlDelete(static function (LteUser $user) {
                    return $user->id !== 1 && admin()->id !== $user->id;
                }),
                $modelTable->disableChecks(),
            )
        );
    }

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  Form  $form
     * @param  Tab  $tab
     * @return Page
     */
    public function matrix(Page $page, Card $card, Form $form, Tab $tab)
    {
        return $page
            ->card(
                $card->title(['lte.add_admin', 'lte.edit_admin']),
                $card->form(
                    $form->tab(
                        $tab->ifEdit()->info_id(),
                        $tab->image('avatar', 'lte.avatar')->nullable(),
                        $tab->icon_cogs()->title('lte.common'),
                        $tab->input('login', 'lte.login_name')
                            ->required()
                            ->unique(LteUser::class, 'login', $this->model()->id),
                        $tab->input('name', 'lte.name')->required(),
                        $tab->email('email', 'lte.email_address')
                            ->required()->unique(LteUser::class, 'email', $this->model()->id),
                        $tab->multi_select('roles[]', 'lte.role')->icon_user_secret()
                            ->options(LteRole::all()->pluck('name', 'id')),
                        $tab->ifEdit()->info_updated_at(),
                        $tab->ifEdit()->info_created_at(),
                    ),
                    $form->if(admin()->isRootAdmin())->tab(
                        $tab->ifEdit()->info_id(),
                        $tab->icon_key()->title('lte.password'),
                        $tab->password('password', 'lte.new_password')
                            ->confirm()->required_condition($this->isType('create')),
                        $tab->ifEdit()->info_updated_at(),
                        $tab->ifEdit()->info_created_at(),
                    ),
                ),
                $card->footer_form(),
            );
    }

    /**
     * @param  Request  $request
     * @param  Page  $page
     * @param  Card  $card
     * @param  ModelInfoTable  $modelInfoTable
     * @param  Tab  $tab
     * @param  ChartJs  $chartJs
     * @param  SearchForm  $searchForm
     * @return Page
     */
    public function show(
        Request $request,
        Page $page,
        Card $card,
        ModelInfoTable $modelInfoTable,
        Tab $tab,
        ChartJs $chartJs,
        SearchForm $searchForm
    ) {
        $logTitles = $this->model()->logs()->distinct('title')->pluck('title');

        return $page
            ->card(
                $card->model_info_table(
                    $modelInfoTable->id(),
                    $modelInfoTable->row('lte.avatar', 'avatar')->avatar(150),
                    $modelInfoTable->row('lte.role', [$this, 'show_role']),
                    $modelInfoTable->row('lte.email_address', 'email'),
                    $modelInfoTable->row('lte.login_name', 'login'),
                    $modelInfoTable->row('lte.name', 'name'),
                    $modelInfoTable->at(),
                )
            )
            ->card(
                $card->title('lte.activity')->warningType(),
                $card->tab(
                    $tab->icon_clock()->title('lte.timeline'),
                    $tab->with(fn($content) => UserController::timelineComponent($content, $this->model()->logs(),
                        $this)),
                ),
                $card->tab(
                    $tab->title('lte.activity')->icon_chart_line(),
                    $tab->active($request->has('q')),
                    $tab->chart_js(
                        $chartJs->model($this->model()->logs())
                            ->hasSearch(
                                $searchForm->date_range('created_at', 'lte.created_at')
                                    ->default(implode(' - ', $this->defaultDateRange()))
                            )
                            ->setDefaultDataBetween('created_at', ...$this->defaultDateRange())
                            ->groupDataByAt('created_at')
                            ->withCollection($logTitles, function ($title) {
                                return $this->chart_js->eachPoint($title, static function ($c) use ($title) {
                                    return $c->where('title', $title)->count();
                                });
                            })->miniChart(),
                    )
                ),
                $card->tab(
                    $tab->title('lte.day_activity')->icon_chart_line(),
                    $tab->chart_js(
                        $chartJs->model($this->model()->logs())
                            ->setDataBetween('created_at', now()->startOfDay(), now()->endOfDay())
                            ->groupDataByAt('created_at', 'H:i')
                            ->withCollection($logTitles, function ($title) {
                                return $this->chart_js->eachPoint($title, function ($c) use ($title) {
                                    return $c->where('title', $title)->count();
                                });
                            })->miniChart(),
                    )
                ),
            );
    }

    public function defaultDateRange()
    {
        return [
            now()->subDay()->startOfDay()->toDateString(),
            now()->endOfDay()->toDateString(),
        ];
    }
}
