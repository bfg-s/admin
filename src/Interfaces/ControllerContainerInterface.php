<?php

namespace LteAdmin\Interfaces;

use LteAdmin\Core\Delegate;
use LteAdmin\Page;

interface ControllerContainerInterface
{
    /**
     * @param  Page  $page
     * @param  Delegate[]  $delegates
     */
    public static function registrationInToContainer(Page $page, array $delegates = []);
}
