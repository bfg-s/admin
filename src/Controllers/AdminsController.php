<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Http\Request;
use Lar\Layout\Respond;
use Lar\LteAdmin\Core\ModelSaver;
use Lar\LteAdmin\Models\LteFileStorage;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Models\LteUser;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class AdminsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('lte::auth.users.list');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('lte::auth.users.create');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        return view('lte::auth.users.edit');
    }

    /**
     * @param  Request  $request
     * @param  Respond  $respond
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, Respond $respond)
    {
        $all = $request->all();

        $validator = \Validator::make($all, [
            'login' => 'required|unique:lte_users',
            'email' => 'required|unique:lte_users',
        ]);

        if ($validator->fails()) {

            return back()->withErrors($validator);
        }


        $admin = new LteUser();

        $admin->login = $all['login'];
        $admin->password = bcrypt($all['password']);
        $admin->email = $all['email'];
        $admin->name = $all['name'];
        $admin->avatar = isset($all['avatar']) ? LteFileStorage::makeFile($all['avatar']) : null;

        if ($admin->save()) {

            $admin->roles()->save(LteRole::find(2));

            $respond->toast_success('Администратор успешно создан!');
        }

        else {

            $respond->toast_error('Неизвестная ошибка при создании администратора!');
        }
        
        return $this->returnTo();
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

                return back()->withErrors($validator);
            }

            else {

                $this->model()->password = bcrypt($all['password']);

                if ($this->model()->save()) {

                    $respond->toast_success('Пароль администратора успешно изменен!');

                    return $this->returnTo();
                }
            }
        }

        else {

            if (ModelSaver::do($this->model(), $all)) {

                $respond->toast_success('Администратор успешно изменен!');

                return $this->returnTo();
            }
        }
    }
}
