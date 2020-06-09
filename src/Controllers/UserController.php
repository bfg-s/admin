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
                    ->card('lte.information')
                    ->primary()
                    ->body()
                    ->view('lte::profile.user_portfolio', ['user' => $this->user]);

                $row->col(9)
                    ->card('lte.edit')
                    ->success()
                    ->body(function (DIV $div) {

                        $div->form($this->user, function (Form $form) {

                            $form->vertical();

                            $form->file('avatar', 'lte.avatar')
                                ->exts('jpg', 'jpeg', 'png');

                            $form->input('login', 'lte.login_name')
                                ->isRequired();

                            $form->email('email', 'lte.email_address')
                                ->isRequired();

                            $form->input('name', 'lte.name')
                                ->isRequired();

                            $form->br()->h5('Password')->hr();

                            $form->password('password', 'lte.new_password')
                                ->confirmed();

                        })->form_footer();

                    });
            });
        });
    }

    /**
     * @param  Request  $request
     * @param  Respond  $respond
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Respond $respond)
    {
        $all = $request->all();

        if ($back = back_validate($all, [
            'password' => 'confirmed',
            'login' => 'required|min:4' . (isset($all['login']) && admin()->email != $all['login'] ? '|unique:' . LteUser::class . ',login' : ''),
            'email' => 'required|email' . (isset($all['email']) && admin()->email != $all['email'] ? '|unique:' . LteUser::class . ',email' : ''),
            'name' => 'required|min:4'
        ])) {
            return $back;
        }

        if ($all['password']) {

            $all['password'] = bcrypt($all['password']);

        } else {

            unset($all['password']);
        }

        if (ModelSaver::do(admin(), $all)) {

            $respond->toast_success(__('lte.profile_success_changed'));

        } else {

            $respond->toast_error(__('lte.unknown_error'));
        }

        return back();
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
