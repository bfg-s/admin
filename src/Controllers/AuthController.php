<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Facades\Admin;
use Admin\Requests\LoginCodeRequest;
use Admin\Requests\LoginOrCodeRequest;
use Admin\Requests\LoginRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Google2FA;

class AuthController
{
    /**
     * Info about admin panel.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'prefix' => config('admin.route.prefix'),
            'dark' => admin_repo()->isDarkMode,
            'langMode' => config('admin.lang_mode'),
            'languages' => config('admin.languages'),
        ]);
    }

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
        $result = $this->authByRequestWithRedirect($request);

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
     * @param  \Admin\Requests\LoginCodeRequest  $request
     * @param  \PragmaRX\Google2FAQRCode\Google2FA  $google2fa
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     */
    public function loginPost(LoginCodeRequest $request, Google2FA $google2fa): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        if ($this->authByRequest($request)) {

            if (admin()->two_factor_confirmed_at) {

                $secret = admin()->two_factor_secret;

                if (! $google2fa->verify($request->code ?: '000000', $secret)) {

                    Auth::guard('admin')->logout();

                    if (Admin::isApiMode()) {

                        return response()->json([
                            'status' => 'error',
                            'message' => __('admin.invalid_code'),
                        ], 422);
                    }

                    return redirect()->route('admin.login');
                }
            }

            if (Admin::isApiMode()) {

                return response()->json([
                    'status' => 'success',
                    'message' => __('admin.success') . '!',
                    'bearer' => Crypt::encrypt(admin()->id),
                ]);
            }

            return redirect()->route(config('admin.home-route', 'admin.dashboard'));
        }

        return Admin::isApiMode()
            ? response()->json([
                'status' => 'error',
                'message' => __('admin.invalid_credentials'),
            ], 422) : redirect()->route('admin.login');
    }

    /**
     * Login processing page for the admin panel.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return bool
     */
    protected function authByRequest(FormRequest $request): bool
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

        return $login;
    }

    /**
     * Login processing page for the admin panel with redirect.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\Http\JsonResponse
     */
    protected function authByRequestWithRedirect(FormRequest $request): \Illuminate\Foundation\Application|Redirector|Application|RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if (
            $this->authByRequest($request)
            && session()->has('return_authenticated_url')
        ) {

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

        $result = $this->authByRequestWithRedirect($request);

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
