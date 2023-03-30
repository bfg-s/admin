<?php

namespace Admin\Controllers;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Lar\Layout\Respond;
use Lar\Layout\Tags\DIV;
use Admin\Components\ModelInfoTableComponent;
use Admin\Components\TabContentComponent;
use Admin\Delegates\Card;
use Admin\Delegates\ChartJs;
use Admin\Delegates\Column;
use Admin\Delegates\Form;
use Admin\Delegates\SearchForm;
use Admin\Delegates\Tab;
use Admin\Models\AdminLog;
use Admin\Models\AdminUser;
use Admin\Page;

class UserController extends Controller
{
    /**
     * Static variable Model.
     * @var string
     */
    public static $model = AdminUser::class;
    /**
     * @var AdminUser
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
            ->breadcrumb('admin.administrator', 'admin.profile')
            ->column(
                $column->num(3)->card(
                    $card
                        ->title('admin.information')
                        ->primaryType()
                        ->card_body()
                        ->view('admin::auth.user_portfolio', ['user' => $this->model()])
                )
            )
            ->column(
                $column->num(9)->card(
                    $card->title('admin.edit')
                        ->successType(),
                    $card->tab(
                        $tab->right(),
                        $tab->active(!$request->has('ltelog_per_page') && !$request->has('ltelog_page') && !$request->has('q')),
                        $tab->icon_cogs()->title('admin.settings'),
                        $tab->form(
                            $form->vertical(),
                            $form->image('avatar', 'admin.avatar'),
                            $form->input('login', 'admin.login_name')
                                ->required()
                                ->unique(AdminUser::class, 'login', $this->model()->id),
                            $form->email('email', 'admin.email_address')
                                ->required()
                                ->unique(AdminUser::class, 'email', $this->model()->id),
                            $form->input('name', 'admin.name')
                                ->required(),
                            $form->divider(__('admin.password')),
                            $form->password('password', 'admin.new_password')
                                ->confirm(),
                        )
                    ),
                    $card->tab(
                        $tab->active(request()->has('ltelog_per_page') || request()->has('ltelog_page')),
                        $tab->icon_history()->title('admin.timeline'),
                        $tab->with(fn(TabContentComponent $content) => static::timelineComponent(
                            $content,
                            $this->model()->logs(),
                            $this
                        ))
                    ),
                    $card->tab(
                        $tab->title('admin.activity')->icon_chart_line(),
                        $tab->active($request->has('q')),
                        $tab->chart_js(
                            $chartJs->model($this->model()->logs())
                                ->hasSearch(
                                    $searchForm->date_range('created_at', 'admin.created_at')
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
                        $tab->title('admin.day_activity')->icon_chart_line(),
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
            $controller->timeline->set_title(static function (AdminLog $log) {
                return $log->title.($log->detail ? " <small>({$log->detail})</small>" : '');
            }),
            $controller->timeline->set_body(static function (DIV $div, AdminLog $log) {
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
        admin_log_success('Changed data', get_class($this->model()), 'far fa-id-card');

        if (isset($form['password']) && $form['password']) {
            admin_log_success('Changed the password', get_class($this->model()), 'fas fa-key');
        }
    }

    /**
     * @return Model|AdminUser|string|null
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
        admin_log_success('Was logout', null, 'fas fa-sign-out-alt');

        Auth::guard('admin')->logout();

        $respond->redirect(route('admin.login'));

        return $respond;
    }
}
