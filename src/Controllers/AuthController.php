<?php

namespace Admin\Controllers;

use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Admin;

use function respond;

class AuthController
{
    /**
     * Make login page.
     */
    public function login()
    {
        if (!Admin::guest()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin::auth.login');
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function login_post(Request $request)
    {
        $request->validate([
            'login' => 'required|min:3|max:191',
            'password' => 'required|min:4|max:191',
        ]);

        $login = false;

        if (Auth::guard('admin')->attempt(
            ['login' => $request->login, 'password' => $request->password],
            $request->remember == 'on' ? true : false
        )) {
            $request->session()->regenerate();

            respond()->toast_success('Was authorized using login!');

            admin_log_success('Was authorized using login', $request->login, 'fas fa-sign-in-alt');

            $login = true;
        } elseif (Auth::guard('admin')->attempt(
            ['email' => $request->login, 'password' => $request->password],
            $request->remember == 'on' ? true : false
        )) {
            $request->session()->regenerate();

            respond()->toast_success('Was authorized using E-Mail!');

            admin_log_success('Was authorized using E-Mail', $request->login, 'fas fa-at');

            $login = true;
        } else {
            respond()->toast_error('User not found!');
        }

        if ($login && session()->has('return_authenticated_url')) {
            return redirect(session()->pull('return_authenticated_url'));
        }

        return redirect($request->headers->get('referer'));
    }
}
