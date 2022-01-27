<?php

namespace Lar\LteAdmin\Controllers;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Lar\Layout\Respond;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\ModelInfoTableComponent;
use Lar\LteAdmin\Components\TabContentComponent;
use Lar\LteAdmin\Delegates\Card;
use Lar\LteAdmin\Delegates\ChartJs;
use Lar\LteAdmin\Delegates\Column;
use Lar\LteAdmin\Delegates\Form;
use Lar\LteAdmin\Delegates\SearchForm;
use Lar\LteAdmin\Delegates\Tab;
use Lar\LteAdmin\Getters\Menu;
use Lar\LteAdmin\Models\LteLog;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Page;

class UserController extends Controller
{
    /**
     * Static variable Model.
     * @var string
     */
    public static $model = LteUser::class;
    /**
     * @var LteUser
     */
    protected $user;

    /**
     * @param  Request  $request
     * @param  Page  $page
     * @param  Column  $column
     * @param  Card  $card
     * @param  Form  $form
     * @param  Tab  $tab
     * @param  ChartJs  $chartJs
     * @param  SearchForm  $searchForm
     * @return Page
     */
    public function index(
        Request $request,
        Page $page,
        Column $column,
        Card $card,
        Form $form,
        Tab $tab,
        ChartJs $chartJs,
        SearchForm $searchForm
    ) {
        $logTitles = $this->model()->logs()->distinct('title')->pluck('title');

        return $page
            ->title($this->model()->name)
            ->icon_user()
            ->breadcrumb('lte.administrator', 'lte.profile')
            ->column(
                $column->num(3)->card(
                    $card
                        ->title('lte.information')
                        ->primaryType()
                        ->card_body()
                        ->view('lte::auth.user_portfolio', ['user' => $this->model()])
                )
            )
            ->column(
                $column->num(9)->card(
                    $card->title('lte.edit')
                        ->successType(),
                    $card->tab(
                        $tab->right(),
                        $tab->active(!$request->has('ltelog_per_page') && !$request->has('ltelog_page') && !$request->has('q')),
                        $tab->icon_cogs()->title('lte.settings'),
                        $tab->form(
                            $form->vertical(),
                            $form->image('avatar', 'lte.avatar'),
                            $form->input('login', 'lte.login_name')
                                ->required()
                                ->unique(LteUser::class, 'login', $this->model()->id),
                            $form->email('email', 'lte.email_address')
                                ->required()
                                ->unique(LteUser::class, 'email', $this->model()->id),
                            $form->input('name', 'lte.name')
                                ->required(),
                            $form->divider(__('lte.password')),
                            $form->password('password', 'lte.new_password')
                                ->confirm(),
                        )
                    ),
                    $card->tab(
                        $tab->active(request()->has('ltelog_per_page') || request()->has('ltelog_page')),
                        $tab->icon_history()->title('lte.timeline'),
                        $tab->with(fn(TabContentComponent $content) => static::timelineComponent($content,
                            $this->model()->logs(), $this))
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
                    $card->footer_form()->withOutRedirectRadios()->setType('edit'),
                )
            );
    }

    public static function timelineComponent($content, $model, Controller $controller)
    {
        $content->div(['col-md-12'])->timeline(
            $controller->timeline->model($model),
            $controller->timeline->set_title(static function (LteLog $log) {
                return $log->title.($log->detail ? " <small>({$log->detail})</small>" : '');
            }),
            $controller->timeline->set_body(static function (DIV $div, LteLog $log) {
                $div->p0()->model_info_table()->model($log)->when(static function (ModelInfoTableComponent $table) {
                    $table->row('IP', 'ip')->copied();
                    $table->row('URL', 'url')->to_prepend_link();
                    $table->row('Route', 'route')->copied();
                    $table->row('Method', 'method')->copied();
                    $table->row('User Agent', 'user_agent')->copied();
                    $table->row('Session ID', 'session_id')->copied();
                    $table->row('WEB ID', 'web_id')->copied();
                });
            }),
        );
    }

    public function defaultDateRange()
    {
        return [
            now()->subDay()->startOfDay()->toDateString(),
            now()->endOfDay()->toDateString(),
        ];
    }

    public function on_updated($form)
    {
        lte_log_success('Changed data', get_class($this->model()), 'far fa-id-card');

        if (isset($form['password']) && $form['password']) {
            lte_log_success('Changed the password', get_class($this->model()), 'fas fa-key');
        }
    }

    /**
     * @return Model|Menu|LteUser|string|null
     */
    public function getModel()
    {
        return admin();
    }

    /**
     * @param  Respond  $respond
     * @return Respond
     */
    public function logout(Respond $respond)
    {
        lte_log_success('Was logout', null, 'fas fa-sign-out-alt');

        Auth::guard('lte')->logout();

        $respond->redirect(route('lte.login'));

        return $respond;
    }
}
