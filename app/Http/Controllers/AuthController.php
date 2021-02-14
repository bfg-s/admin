<?php

namespace Admin\Http\Controllers;

use Admin\Attributes\AdminPage;
use Admin\Components\ServicePages\Login;
use Illuminate\Http\Request;

/**
 * Class AuthController
 * @package Admin\Http\Controllers
 */
class AuthController extends Controller {

    /**
     * @return string
     */
    #[AdminPage('login')]
    public function loginForm()
    {
        return Login::create(function () {

            Login\Form::create();

            Login\Footer::toSlot('footer');
        });
    }

    /**
     * @param  Request  $request
     * @return string
     */
    public function login(Request $request)
    {
        return 'login';
    }

    /**
     * @return string
     */
    #[AdminPage('logout')]
    public function logout()
    {
        return 'logout';
    }
}