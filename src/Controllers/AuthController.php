<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Facades\Admin;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Google2FA;

class AuthController
{
    /**
     * Admin panel login page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function login(): \Illuminate\View\View|RedirectResponse
    {
        if (!Admin::guest()) {
            return redirect()->route(config('admin.home-route', 'admin.dashboard'));
        }

        return admin_view('login');
    }

    /**
     * Security definition page with input of 2FA admin panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Routing\Redirector|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
     */
    public function twoFa(Request $request): View|Factory|Redirector|\Illuminate\View\View|Application|RedirectResponse
    {
        $result = $this->loginPost($request);

        if (Admin::guest()) {
            return redirect()->route('admin.login');
        }

        if (!admin()->two_factor_confirmed_at) {
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

    /**
     * Login processing page for the admin panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function loginPost(Request $request): \Illuminate\Foundation\Application|Redirector|Application|RedirectResponse
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
            session()->flash('message', 'User not found!');
        }

        if ($login && session()->has('return_authenticated_url')) {
            return redirect(session()->pull('return_authenticated_url'));
        }

        return redirect($request->headers->get('referer'));
    }

    /**
     * Page for entering 2FA for the admin panel.
     *
     * @return RedirectResponse
     */
    public function twoFaGet(): RedirectResponse
    {
        if (!Admin::guest()) {
            return redirect()->route(config('admin.home-route', 'admin.dashboard'));
        }

        return redirect()->route('admin.login');
    }

    /**
     * Page for processing 2FA code for the admin panel.
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     * @throws InvalidCharactersException
     */
    public function twoFaPost(Request $request): View|Factory|\Illuminate\Foundation\Application|Redirector|Application|RedirectResponse
    {
        $data = $request->validate([
            'login' => 'required|min:3|max:191',
            'password' => 'required|min:4|max:191',
            'code' => 'required|min:6|max:6',
        ]);

        $result = $this->loginPost($request);

        if (!admin()) {
            return redirect()->route('admin.login');
        }

        $google2fa = new Google2FA();
        $secret = admin()->two_factor_secret;

        if (!$google2fa->verify($data['code'], $secret)) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login');
        }

        return $result;
    }
}
