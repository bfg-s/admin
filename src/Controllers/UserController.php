<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Http\Request;
use Lar\Layout\Respond;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\ModelSaver;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\Container;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\Row;
use Lar\LteAdmin\Segments\Tagable\Tabs;

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
        $this->user = \LteAdmin::user();

        return Container::create(function (DIV $div, Container $container) {

            $container->title(\LteAdmin::user()->name)
                ->icon_user()
                ->breadcrumb('lte.administrator', 'lte.profile');

            $div->row(function (Row $row) {

                $row->col(3)
                    ->card('Portfolio')
                    ->primary()
                    ->body()
                    ->view('lte::profile.user_window', ['user' => $this->user]);

                $row->col(9)
                    ->card('Edit')
                    ->success()
                    ->body(function (DIV $div) {

                        $div->tabs(function (Tabs $tabs) {

                            $tabs->tab('lte.common', 'fas fa-cogs')->form($this->user, function (Form $form) {

                                $form->vertical();

                                $form->file('avatar', 'lte.avatar')
                                    ->exts('jpg', 'jpeg', 'png');

                                $form->input('login', 'lte.login_name')
                                    ->isRequired();

                                $form->email('email', 'lte.email_address')
                                    ->isRequired();

                                $form->input('name', 'lte.name')
                                    ->isRequired();
                            })->form_footer();

                            $tabs->tab('lte.change_password', 'fas fa-key')->form($this->user, function (Form $form) {

                                $form->vertical();

                                $form->password('password', 'lte.new_password')
                                    ->confirmed();
                            })->form_footer();
                        });

                    })->p0();
            });
        });

        return view('lte::auth.profile', [
            'page_info' => [
                'icon' => 'fas fa-user',
                'title' => \LteAdmin::user()->name,
            ],
            'breadcrumb' => [
                __('lte.administrator'),
                __('lte.profile')
            ],
            'user' => \LteAdmin::user()
        ]);
    }

    /**
     * @param  Request  $request
     * @param  Respond  $respond
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Respond $respond)
    {
        $all = request()->all();

        if ($request->has('ch_password')) {

            $validator = \Validator::make($all, [
                'password' => 'required|confirmed|min:4'
            ]);

            if ($validator->fails()) {

                foreach ($validator->errors()->all() as $item) {

                    $respond->toast_error($item);
                }

                return back()->withErrors($validator);
            }

            else {

                admin()->password = bcrypt($all['password']);

                if (admin()->save()) {

                    $respond->toast_success(__('lte.password_changed_success'));

                    return back();
                }
            }
        }

        else {

            $validator = \Validator::make($all, [
                'login' => 'required|min:4',
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {

                foreach ($validator->errors()->all() as $item) {

                    $respond->toast_error($item);
                }

                return back()->withErrors($validator);
            }

            else {

                if (ModelSaver::do(admin(), $all)) {

                    $respond->toast_success(__('lte.profile_success_changed'));

                    return back();
                }
            }
        }
    }

    /**
     * @param  Respond  $respond
     * @return Respond
     */
    public function logout(Respond $respond)
    {
        \Auth::guard('lte')->logout();

        $respond->redirect(route('lte.login'));

        return $respond;
    }
}
