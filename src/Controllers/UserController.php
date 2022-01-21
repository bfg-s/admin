<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Http\Request;
use Lar\Layout\Respond;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\FormFooterComponent;
use Lar\LteAdmin\Components\ModelInfoTableComponent;
use Lar\LteAdmin\Components\TabContentComponent;
use Lar\LteAdmin\Components\TimelineComponent;
use Lar\LteAdmin\Core\Container;
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|Container
     */
    public function index(Page $page, Request $request)
    {
        return $page
            ->title($this->model()->name)
            ->icon_user()
            ->breadcrumb('lte.administrator', 'lte.profile')
            ->row(
                $this->row->column()->num(3)->card(
                    $this->card
                        ->title('lte.information')
                        ->primaryType()
                        ->body()
                        ->view('lte::auth.user_portfolio', ['user' => $this->model()])
                ),
                $this->row->column()->num(9)->card(
                    $this->card->title('lte.edit')
                        ->successType()
                        ->bodyForm(
                            $this->form->vertical(),
                            $this->form->tab('lte.settings', 'fas fa-cogs', function (TabContentComponent $content) {
                                $content->image('avatar', 'lte.avatar');

                                $content->input('login', 'lte.login_name')
                                    ->required()
                                    ->unique(LteUser::class, 'login', $this->model()->id);

                                $content->email('email', 'lte.email_address')
                                    ->required()
                                    ->unique(LteUser::class, 'email', $this->model()->id);

                                $content->input('name', 'lte.name')
                                    ->required();

                                $content->divider(__('lte.password'));

                                $content->password('password', 'lte.new_password')
                                    ->confirm();
                            }, ! $request->has('ltelog_per_page') && ! $request->has('ltelog_page')),
                            $this->form->tab('lte.timeline', 'fas fa-history', function (TabContentComponent $content) {
                                static::timelineComponent($content, $this->model()->logs());
                            }, request()->has('ltelog_per_page') || request()->has('ltelog_page')),
                            $this->form->tab('lte.day_activity', 'fas fa-chart-line', function (TabContentComponent $content) {
                                static::activityDayComponent($content, $this->model()->logs());
                            }),
                            $this->form->tab('lte.year_activity', 'fas fa-chart-line', function (TabContentComponent $content) {
                                static::activityYearComponent($content, $this->model()->logs());
                            }),
                        ),

                    $this->card->footerForm()->noRedirect()->when(static function (FormFooterComponent $footer) {
                        $footer->setType('edit');
                    })
                )
            );
    }

    public function on_updated($form)
    {
        if (isset($form['password']) && $form['password']) {
            lte_log_success('Changed the password', get_class($this->model()), 'fas fa-key');
        } else {
            lte_log_success('Changed data', get_class($this->model()), 'far fa-id-card');
        }
    }

    public static function activityDayComponent(TabContentComponent $content, $model)
    {
        $chart = $content->chart_js()
            ->model($model)
            ->setDataBetween('created_at', now()->startOfDay(), now()->endOfDay())
            ->groupDataByAt('created_at', 'H:i');

        foreach ($model->distinct('title')->pluck('title') as $item) {
            $chart->eachPoint($item, static function ($c) use ($item) {
                return $c->where('title', $item)->count();
            });
        }
        $chart->miniChart();
    }

    public static function activityYearComponent(TabContentComponent $content, $model)
    {
        $chart = $content->chart_js()
            ->model($model)
            ->setDataBetween('created_at', now()->startOfYear()->startOfDay(), now()->endOfDay())
            ->groupDataByAt('created_at');

        foreach ($model->distinct('title')->pluck('title') as $item) {
            $chart->eachPoint($item, static function ($c) use ($item) {
                return $c->where('title', $item)->count();
            });
        }
        $chart->miniChart();
    }

    public static function timelineComponent($content, $model)
    {
        $content->div(['col-md-12'])->timeline($model, static function (TimelineComponent $timeline) {
            $timeline->set_title(static function (LteLog $log) {
                return $log->title.($log->detail ? " <small>({$log->detail})</small>" : '');
            });

            $timeline->set_body(static function (DIV $div, LteLog $log) {
                $div->p0()->model_info_table()->model($log)->when(static function (ModelInfoTableComponent $table) {
                    $table->row('IP', 'ip')->copied();
                    $table->row('URL', 'url')->to_prepend_link();
                    $table->row('Route', 'route')->copied();
                    $table->row('Method', 'method')->copied();
                    $table->row('User Agent', 'user_agent')->copied();
                    $table->row('Session ID', 'session_id')->copied();
                    $table->row('WEB ID', 'web_id')->copied();
                });
            });
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|\Lar\LteAdmin\Getters\Menu|LteUser|string|null
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

        \Auth::guard('lte')->logout();

        $respond->redirect(route('lte.login'));

        return $respond;
    }
}
