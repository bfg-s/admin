<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Http\Request;

/**
 * Class AuthController
 *
 * @package Lar\LteAdmin\Controllers
 */
class AuthController
{
    /**
     * Make login page
     */
    public function login()
    {
        if (!\LteAdmin::guest()) {

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

        if (\Auth::guard('lte')->attempt(['login' => $request->login, 'password' => $request->password], $request->remember=='on' ? true : false)) {

            $request->session()->regenerate();

            \respond()->toast_success("User success auth by Login");
        }

        else if (\Auth::guard('lte')->attempt(['email' => $request->login, 'password' => $request->password], $request->remember=='on' ? true : false)) {

            $request->session()->regenerate();

            \respond()->toast_success("User success auth by E-Mail");
        }

        else {

            \respond()->toast_error("User not found!");
        }

        return redirect($request->headers->get('referer'));
    }
}
