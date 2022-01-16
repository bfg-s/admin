<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Lar\Layout\Respond;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\ModelSaver;
use Lar\LteAdmin\Models\LteLog;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\Container;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Cores\ChartJsBuilder;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\FormFooter;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\Row;
use Lar\LteAdmin\Segments\Tagable\TabContent;
use Lar\LteAdmin\Segments\Tagable\Tabs;
use Lar\LteAdmin\Segments\Tagable\Timeline;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class UserController extends Controller
{
    /**
     * @var LteUser
     */
    protected $user;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|Container
     */
    public function index()
    {
        return Container::create(function (DIV $div, Container $container) {

            $container->title($this->model()->name)
                ->icon_user()
                ->breadcrumb('lte.administrator', 'lte.profile');

            $div->row(function (Row $row) {

                $row->col(3)
                    ->card('lte.information')
                    ->primary()
                    ->body()
                    ->view('lte::auth.user_portfolio', ['user' => $this->model()]);

                $card = $row->col(9)
                    ->card('lte.edit');

                $card->success()
                    ->fullBody($this->matrix());

                $card->footerForm(false, function (FormFooter $footer) {
                    $footer->setType('edit');
                });
            });
        });
    }

    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\LarDoc|Form
     */
    public function matrix()
    {
        return Form::create(function (Form $form) {

            $form->vertical();

            $form->tab('lte.settings', 'fas fa-cogs', function (TabContent $content) {

                $content->image('avatar', 'lte.avatar');

                $content->input('login', 'lte.login_name')
                    ->required()
                    ->unique(LteUser::class, 'login', $this->model()->id);

                $content->email('email', 'lte.email_address')
                    ->required()
                    ->unique(LteUser::class, 'email', $this->model()->id);

                $content->input('name', 'lte.name')
                    ->required();

                $content->br()->h5(__('lte.password'))->hr();

                $content->password('password', 'lte.new_password')
                    ->confirm();
            }, !request()->has('ltelog_per_page') && !request()->has('ltelog_page'));

            $form->tab('lte.timeline', 'fas fa-history', function (TabContent $content) {

                static::timelineComponent($content, $this->model()->logs());
            }, request()->has('ltelog_per_page') || request()->has('ltelog_page'));

            $form->tab('lte.day_activity', 'fas fa-chart-line', function (TabContent $content) {

                static::activityDayComponent($content, $this->model()->logs());
            });

            $form->tab('lte.year_activity', 'fas fa-chart-line', function (TabContent $content) {

                static::activityYearComponent($content, $this->model()->logs());
            });


            ModelSaver::on_updated(get_class($this->model()), function ($form) {
                if (isset($form['password']) && $form['password']) {
                    lte_log_success('Changed the password', get_class($this->model()), 'fas fa-key');
                } else {
                    lte_log_success('Changed data', get_class($this->model()), 'far fa-id-card');
                }
            });
        });
    }

    public static function activityDayComponent(TabContent $content, $model)
    {
        $content->chart_js($model)
            ->setDataBetween('created_at', now()->startOfDay(), now()->endOfDay())
            ->groupDataByAt('created_at', 'H:i')
            ->eachPointCount('lte.activity')
            ->eachPoint('lte.page_loadings', function ($c) { return $c->where('method', 'GET')->count(); })
            ->eachPoint('lte.creates', function ($c) { return $c->where('method', 'POST')->count(); })
            ->eachPoint('lte.updates', function ($c) { return $c->where('method', 'PUT')->count(); })
            ->eachPoint('lte.deletes', function ($c) { return $c->where('method', 'DELETE')->count(); })
            ->miniChart();
    }

    public static function activityYearComponent(TabContent $content, $model)
    {
        $content->chart_js($model)
            ->setDataBetween('created_at', now()->startOfYear()->startOfDay(), now()->endOfDay())
            ->groupDataByAt('created_at')
            ->eachPointCount('lte.activity')
            ->eachPoint('lte.page_loadings', function ($c) { return $c->where('method', 'GET')->count(); })
            ->eachPoint('lte.creates', function ($c) { return $c->where('method', 'POST')->count(); })
            ->eachPoint('lte.updates', function ($c) { return $c->where('method', 'PUT')->count(); })
            ->eachPoint('lte.deletes', function ($c) { return $c->where('method', 'DELETE')->count(); })
            ->miniChart();
    }

    public static function timelineComponent($content, $model)
    {
        $content->div(['col-md-12'])->timeline($model, function (Timeline $timeline) {

            $timeline->set_title(function (LteLog $log) {

                return $log->title . ($log->detail ? " <small>({$log->detail})</small>":"");
            });

            $timeline->set_body(function (DIV $div, LteLog $log) {

                $div->p0()->model_info_table($log, function (ModelInfoTable $table) {

                    $table->row('IP', 'ip')->copied();
                    $table->row('URL', 'url')->copied();
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
    public function model()
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
