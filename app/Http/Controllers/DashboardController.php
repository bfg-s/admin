<?php

namespace Admin\Http\Controllers;

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
}