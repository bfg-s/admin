<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Controllers\Generators\DashboardGenerator;
use Lar\LteAdmin\Segments\Container;

/**
 * Class DashboardController
 *
 * @package Lar\LteAdmin\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @return Container
     */
    public function index()
    {
        return Container::create(function (DashboardGenerator $generator) {

            $generator->aboutServer();
        });
    }
}
