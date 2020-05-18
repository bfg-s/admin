<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Http\Request;
use Lar\Layout\Respond;
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
        $roles = function (LteUser $user) {

            return '<span class="badge badge-success">' . $user->roles->pluck('name')->implode('</span> <span class="badge badge-success">') . '</span>';
        };

        return view('lte::auth.users.list', [
            'roles' => $roles
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('lte::auth.users.create', [
            'roles' => LteRole::all()->pluck('name','id')
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        return view('lte::auth.users.edit', [
            'roles' => LteRole::all()->pluck('name','id')
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('lte::auth.users.show');
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

        /** @var LteUser $user_model */
        $user_model = config('lte.auth.providers.lte.model');
        $admin = new $user_model();

        $admin->login = $all['login'];
        $admin->password = bcrypt($all['password']);
        $admin->email = $all['email'];
        $admin->name = $all['name'];
        $admin->avatar = isset($all['avatar']) ? LteFileStorage::makeFile($all['avatar']) : null;

        if ($admin->save()) {

            $admin->roles()->sync($all['roles']);

            $respond->toast_success(__('lte.successfully_created'));
        }

        else {

            $respond->toast_error(__('lte.unknown_error'));
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

                    $respond->toast_success(__('lte.password_changed_success'));

                    return $this->returnTo();
                }
            }
        }

        else {

            if ($this->model()->update($all)) {

                $this->model()->roles()->sync($all['roles']);

                $respond->toast_success(__('lte.saved_successfully'));

                return $this->returnTo();
            }
        }
    }
}
