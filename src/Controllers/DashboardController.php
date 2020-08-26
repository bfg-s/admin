<?php

namespace Lar\LteAdmin\Controllers;

use Lar\Layout\Tags\DIV;
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
        return Container::create(function (DIV $div, DashboardGenerator $generator) {

            $generator->aboutServer($div);
        });
    }
}
