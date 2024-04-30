<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Facades\AdminFacade;
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

use function respond;

class AuthController
{
    /**
     * Make login page.
     */
    public function login()
    {
        if (!AdminFacade::guest()) {
            return redirect()->route(config('admin.home-route', 'admin.dashboard'));
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
     * @param  Request  $request
     * @return Application|Factory|View|RedirectResponse|Redirector
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
            session()->flash('message', 'User not found!');
        }

        if ($login && session()->has('return_authenticated_url')) {
            return redirect(session()->pull('return_authenticated_url'));
        }

        return redirect($request->headers->get('referer'));
    }

    /**
     * @return RedirectResponse
     */
    public function twofaGet()
    {
        if (!AdminFacade::guest()) {
            return redirect()->route(config('admin.home-route', 'admin.dashboard'));
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

        if (!$google2fa->verify($data['code'], $secret)) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login');
        }

        return $result;
    }
}
