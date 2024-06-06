<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Facades\Admin;
use Admin\Requests\LoginCodeRequest;
use Admin\Requests\LoginRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
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
     * @param  \Admin\Requests\LoginRequest  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Routing\Redirector|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function twoFa(LoginRequest $request): View|Factory|Redirector|\Illuminate\View\View|Application|RedirectResponse|\Illuminate\Http\JsonResponse
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
     * @param  \Admin\Requests\LoginRequest|\Admin\Requests\LoginCodeRequest  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\Http\JsonResponse
     */
    public function loginPost(LoginRequest|LoginCodeRequest $request): \Illuminate\Foundation\Application|Redirector|Application|RedirectResponse|\Illuminate\Http\JsonResponse
    {
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
     * @param  \Admin\Requests\LoginCodeRequest  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     */
    public function twoFaPost(LoginCodeRequest $request): View|Factory|\Illuminate\Foundation\Application|Redirector|Application|RedirectResponse
    {
        $data = $request->validated();

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
