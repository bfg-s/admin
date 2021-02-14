<?php

namespace Admin\Http\Controllers;

use Admin\Attributes\AdminPage;
use Admin\Components\ServicePages\Login;

/**
 * Class DashboardController
 * @package Admin\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @return string
     */
    #[AdminPage('/', 'home')] public function index()
    {
        //respond('alert', 'hi!');

        return "Home";
    }

    /**
     * @return mixed
     */
    #[AdminPage('administrators')] public function administrators(): mixed {
        return "Administrators";
    }

    /**
     * @return string
     */
    #[AdminPage('menu')] public function index2()
    {
        return Login::create(function () {

            Login\Form::create();

            Login\Footer::create();
        });
    }
}