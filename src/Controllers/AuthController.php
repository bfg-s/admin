<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Http\Request;

class AuthController
{
    /**
     * Make login page.
     */
    public function login()
    {
        if (! \LteAdmin::guest()) {
            return redirect()->route('lte.dashboard');
        }

        return view('lte::auth.login');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login_post(Request $request)
    {
        $request->validate([
            'login' => 'required|min:3|max:191',
            'password' => 'required|min:4|max:191',
        ]);

        $login = false;

        if (\Auth::guard('lte')->attempt(['login' => $request->login, 'password' => $request->password], $request->remember == 'on' ? true : false)) {
            $request->session()->regenerate();

            \respond()->toast_success('Was authorized using login!');

            lte_log_success('Was authorized using login', $request->login, 'fas fa-sign-in-alt');

            $login = true;
        } elseif (\Auth::guard('lte')->attempt(['email' => $request->login, 'password' => $request->password], $request->remember == 'on' ? true : false)) {
            $request->session()->regenerate();

            \respond()->toast_success('Was authorized using E-Mail!');

            lte_log_success('Was authorized using E-Mail', $request->login, 'fas fa-at');

            $login = true;
        } else {
            \respond()->toast_error('User not found!');
        }

        if ($login && session()->has('return_authenticated_url')) {
            return redirect(session()->pull('return_authenticated_url'));
        }

        return redirect($request->headers->get('referer'));
    }
}
