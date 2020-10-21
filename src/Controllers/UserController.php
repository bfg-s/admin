<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Lar\Layout\Respond;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\ModelSaver;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\Container;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\FormFooter;
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
                    ->body($this->matrix());

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

            $form->image('avatar', 'lte.avatar');

            $form->input('login', 'lte.login_name')
                ->required()
                ->unique(LteUser::class, 'login', $this->model()->id);

            $form->email('email', 'lte.email_address')
                ->required()
                ->unique(LteUser::class, 'email', $this->model()->id);

            $form->input('name', 'lte.name')
                ->required();

            $form->br()->h5(__('lte.password'))->hr();

            $form->password('password', 'lte.new_password')
                ->confirm();
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
        \Auth::guard('lte')->logout();

        $respond->redirect(route('lte.login'));

        return $respond;
    }
}
