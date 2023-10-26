<?php

namespace Admin\Controllers;

use Admin\Facades\AdminFacade;
use Admin\Respond;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Google2FA;

use Illuminate\Support\Facades\Crypt;

use function respond;

class AuthController
{
    /**
     * Make login page.
     */
    public function login()
    {
        if (!AdminFacade::guest()) {
            return redirect()->route('admin.dashboard');
        }

        return admin_view('login');
    }

    /**
     * Make login page.
     */
    public function twofa(Request $request)
    {
        $result = $this->login_post($request);

        if (AdminFacade::guest()) {
            return redirect()->route('admin.login');
        }

        if (! admin()->two_factor_confirmed_at) {
            return $result;
        } else {
            Auth::guard('admin')->logout();
        }

        return admin_view('2fa', [
            'login' => $request->login,
            'password' => $request->password,
            'remember' => $request->remember,
        ]);
    }

    public function twofaGet(Request $request)
    {
        if (!AdminFacade::guest()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('admin.login');
    }

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     * @throws InvalidCharactersException
     */
    public function twofaPost(Request $request)
    {
        $data = $request->validate([
            'login' => 'required|min:3|max:191',
            'password' => 'required|min:4|max:191',
            'code' => 'required|min:6|max:6',
        ]);

        $result = $this->login_post($request);

        if (!admin()) {
            return redirect()->route('admin.login');
        }

        $google2fa = new Google2FA();
        $secret = admin()->two_factor_secret;

        if (! $google2fa->verify($data['code'], $secret)) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login');
        }

        return $result;
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse|\Illuminate\Routing\Redirector
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
            $request->remember === 'on'
        )) {
            $request->session()->regenerate();

            admin_log_success('Was authorized using login', $request->login, 'fas fa-sign-in-alt');

            $login = true;
        } elseif (Auth::guard('admin')->attempt(
            ['email' => $request->login, 'password' => $request->password],
            $request->remember === 'on'
        )) {
            $request->session()->regenerate();

            admin_log_success('Was authorized using E-Mail', $request->login, 'fas fa-at');

            $login = true;
        } else {
            //Respond::glob()->toast_error('User not found!');
            session()->flash('message', 'User not found!');
        }

        if ($login && session()->has('return_authenticated_url')) {
            return redirect(session()->pull('return_authenticated_url'));
        }

        return redirect($request->headers->get('referer'));
    }
}
