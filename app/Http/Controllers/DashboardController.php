<?php

namespace Admin\Http\Controllers;

use Admin\Components\ServicePages\Login;

/**
 * Class DashboardController
 * @package Admin\Http\Controllers
 */
class DashboardController extends Controller {

    /**
     * @return string
     */
    public function index()
    {
        return "Dashboard";
    }

    /**
     * @return string
     */
    public function index2()
    {
        return Login::create(function () {

            Login\Form::create();

            Login\Footer::toSlot('footer');
        });
    }
}