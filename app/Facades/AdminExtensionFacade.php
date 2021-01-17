<?php

namespace Admin\Facades;


use Admin\AdminExtension;
use Illuminate\Support\Facades\Facade;

/**
 * Class AdminExtensionFacade
 * @package Admin\Facades
 */
class AdminExtensionFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return AdminExtension::class;
    }
}
