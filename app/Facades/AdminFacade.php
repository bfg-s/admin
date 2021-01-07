<?php

namespace Admin\Facades;

use Admin\Admin;
use Illuminate\Support\Facades\Facade;

/**
 * Class AdminFacade
 * @package Admin\Facades
 */
class AdminFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Admin::class;
    }
}
