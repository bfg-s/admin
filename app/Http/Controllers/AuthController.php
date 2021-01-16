<?php

namespace Admin\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class AuthController
 * @package Admin\Http\Controllers
 */
class AuthController extends Controller {

    /**
     * @return string
     */
    public function loginForm()
    {
        return "login form";
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
    public function logout()
    {
        return 'logout';
    }
}